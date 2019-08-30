import fetch from 'isomorphic-unfetch'
import { skills, maxF2PTotal, levelThresholds } from './constants'

/**
 * Returns the virtual level given skill level and xp
 * @param {number} level
 * @param {number} xp
 * @returns {number}
 */

export function calculateVirtualLevel(level, xp) {
  if (xp < 14391160) {
    return level
  }

  if (xp === 200000000) {
    return 126
  }

  let virtualLevel = 99

  for (let i = 99; i < 127; i += 1) {
    if (xp < levelThresholds[i]) {
      virtualLevel = i - 1
      break
    }
  }

  return virtualLevel
}

/**
 * Converts the raw rs hiscores lit API string into a stats object
 * @param {string} raw
 * @returns {Object}
 */

export function parseRawStats(raw) {
  if (!raw) {
    throw new Error('Raw stats are required for parsing')
  }

  const stats = {}

  try {
    const lines = raw.split('\n')

    lines.map((line, i) => {
      const skill = skills.rs3.allWithTotal[i]
      const [rank, level, xp] = line.split(',').map(x => parseInt(x, 10))

      stats[skill] = {
        rank,
        level,
        virtualLevel: calculateVirtualLevel(level, xp),
        xp
      }
    })
  } catch (err) {
    throw new Error('Malformed raw hiscores data.')
  }

  return stats
}

/**
 * Returns the raw string from the rs hiscores lite API
 * @param {string} player
 * @returns {string}
 */

export async function getRawStats(player) {
  if (!player) {
    throw new Error('Player name is required.')
  }

  const res = await fetch(
    `https://secure.runescape.com/m=hiscore/index_lite.ws?player=${player}`
  )

  if (res.status === 404) {
    throw {
      status: 404,
      code: 'not_found',
      message: 'Player not found'
    }
  }

  if (res.status !== 200) {
    throw {
      status: 500,
      code: 'server_error',
      message: 'Failed to fetch data'
    }
  }

  return res.text()
}

/**
 * Returns true if stats represent a P2P player
 * @param {Object} stats
 * @returns {boolean}
 */

export function isPlayerP2P(stats) {
  if (!stats) {
    throw new Error('Stats object must not be undefined')
  }

  if (
    // Total level is above the max possible
    stats.total.level > maxF2PTotal.rs3 ||
    // Above level 1 in any P2P skills
    skills.rs3.p2p.every(skill => stats[skill].level !== 1)
  ) {
    return true
  }

  return false
}

/**
 * Returns 'normal' if stats contain combat level,
 * otherwise it returns 'skiller'
 * @param {Object} stats
 * @returns {string}
 */

export function getPlayerMode(stats) {
  if (!stats) {
    throw new Error('Stats object must not be undefined')
  }

  if (
    stats.attack.xp === 0 &&
    stats.defence.xp === 0 &&
    stats.strength.xp === 0 &&
    stats.constitution.xp === 0 &&
    stats.ranged.xp === 0 &&
    stats.prayer.xp === 0 &&
    stats.magic.xp === 0
  ) {
    return 'skiller'
  }

  return 'normal'
}

/**
 * Returns the skiller total of the given stats
 * @param {Object} stats
 * @returns {number}
 */

export function calculateSkillerTotal(stats) {
  if (!stats) {
    throw new Error('Stats object must not be undefined')
  }

  return (
    stats.cooking.level +
    stats.woodcutting.level +
    stats.fletching.level +
    stats.fishing.level +
    stats.firemaking.level +
    stats.crafting.level +
    stats.smithing.level +
    stats.mining.level +
    stats.runecrafting.level +
    stats.dungeoneering.level
  )
}

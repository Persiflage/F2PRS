import fetch from 'isomorphic-unfetch'

const maxF2PTotal = 1750
const numP2PSkills = 10

const f2pSkills = [
  'attack',
  'defence',
  'strength',
  'constitution',
  'ranged',
  'prayer',
  'magic',
  'cooking',
  'woodcutting',
  'fletching',
  'fishing',
  'firemaking',
  'crafting',
  'smithing',
  'mining',
  'runecrafting',
  'dungeoneering'
]

const skills = [
  'total',
  'attack',
  'defence',
  'strength',
  'constitution',
  'ranged',
  'prayer',
  'magic',
  'cooking',
  'woodcutting',
  'fletching',
  'fishing',
  'firemaking',
  'crafting',
  'smithing',
  'mining',
  'herblore',
  'agility',
  'thieving',
  'slayer',
  'farming',
  'runecrafting',
  'hunter',
  'construction',
  'summoning',
  'dungeoneering',
  'divination',
  'invention'
]

const fakeStats = `437529,1704,1314316802
101884,99,17426903
857105,55,172828
177067,99,13759534
207223,99,16642264
186162,99,13454969
8684,99,41313512
23377,99,80182512
1361,99,200000000
19413,99,27668873
191902,97,10777745
2550,99,166152336
10054,99,98485437
9049,99,37876589
307,99,200000000
25122,99,35581340
-1,1,-1
-1,1,-1
-1,1,-1
-1,1,-1
-1,1,-1
895,99,200000000
-1,1,-1
-1,1,-1
-1,1,-1
12918,120,154818468
-1,1,-1
-1,0,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1
77037,39
311,12000
-1,-1
268604,2700
-1,-1
-1,-1
-1,-1
-1,-1
-1,-1`

/**
 * Returns the virtual level given skill level and xp
 * @param {number} level
 * @param {number} xp
 * @returns {number}
 */

function calculateVirtualLevel(level, xp) {
  if (xp < 14391160) {
    return level
  }

  if (xp === 200000000) {
    return 126
  }

  /* eslint-disable-next-line */
  const levelThresholds = [
    0,
    0,
    83,
    174,
    276,
    388,
    512,
    650,
    801,
    969,
    1154,
    1358,
    1584,
    1833,
    2107,
    2411,
    2746,
    3115,
    3523,
    3973,
    4470,
    5018,
    5624,
    6291,
    7028,
    7842,
    8740,
    9730,
    10824,
    12031,
    13363,
    14833,
    16456,
    18247,
    20224,
    22406,
    24815,
    27473,
    30408,
    33648,
    37224,
    41171,
    45529,
    50339,
    55649,
    61512,
    67983,
    75127,
    83014,
    91721,
    101333,
    111945,
    123660,
    136594,
    150872,
    166636,
    184040,
    203254,
    224466,
    247886,
    273742,
    302288,
    333804,
    368599,
    407015,
    449428,
    496254,
    547953,
    605032,
    668051,
    737637,
    814445,
    899257,
    992895,
    1096278,
    1210421,
    1336443,
    1475581,
    1629200,
    1798808,
    1986068,
    2192818,
    2421087,
    2673114,
    2951373,
    3258594,
    3597792,
    3972294,
    4385776,
    4842295,
    5346332,
    5902831,
    6517253,
    7195629,
    7944614,
    8771558,
    9684577,
    10692629,
    11805606,
    13034431,
    14391160,
    15889109,
    17542976,
    19368992,
    21385073,
    23611006,
    26068632,
    28782069,
    31777943,
    35085654,
    38737661,
    42769801,
    47221641,
    52563718,
    57563718,
    63555443,
    70170840,
    77474828,
    85539082,
    94442737,
    104273167,
    115126838,
    127110260,
    140341028,
    154948977,
    171077457,
    188884740,
    200000000
  ]

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

function parseRawStats(raw) {
  const stats = {}

  const lines = raw.split('\n')
  for (let i = 0; i < skills.length; i += 1) {
    const skill = skills[i]
    let [rank, level, xp] = lines[i].split(',')
    ;[rank, level, xp] = [rank, level, xp].map(x => parseInt(x, 10))

    stats[skill] = {
      rank,
      level,
      virtualLevel: calculateVirtualLevel(level, xp),
      xp
    }
  }

  return stats
}

/**
 * Returns the raw string from the rs hiscores lite API
 * @param {string} player
 * @returns {string}
 */

async function getRawStats(player) {
  // TODO: uncomment
  // const res = await fetch(`https://secure.runescape.com/m=hiscore/index_lite.ws?player=${player}`);
  // return res.text();

  // TODO: delete this dummy version
  return fakeStats
}

/**
 * Returns true is stats contain P2P levels
 * @param {Object} stats
 * @returns {boolean}
 */

function isPlayerP2P(stats) {
  if (
    stats.herblore.level !== 1 ||
    stats.agility.level !== 1 ||
    stats.thieving.level !== 1 ||
    stats.slayer.level !== 1 ||
    stats.farming.level !== 1 ||
    stats.hunter.level !== 1 ||
    stats.construction.level !== 1 ||
    stats.summoning.level !== 1 ||
    stats.divination.level !== 1 ||
    stats.invention.level !== 1 ||
    stats.total.level > maxF2PTotal
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

function getPlayerMode(stats) {
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
 * Returns the F2P total of the given stats
 * @param {Object} stats
 * @returns {number}
 */

function calculateF2PTotal(stats) {
  let total = numP2PSkills

  f2pSkills.forEach(skill => {
    total += stats[skill].level
  })

  return total
}

/**
 * Returns the skiller total of the given stats
 * @param {Object} stats
 * @returns {number}
 */

function calculateSkillerTotal(stats) {
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

module.exports = {
  getPlayerMode,
  isPlayerP2P,
  calculateF2PTotal,
  calculateSkillerTotal,
  calculateVirtualLevel,
  getRawStats,
  parseRawStats
}

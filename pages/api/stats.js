import { getRawStats, parseRawStats } from '../../lib/rs-api'

export default async (req, res) => {
  // Only allow for GET requests
  if (req.method !== 'GET') {
    res.status(405).json({
      code: 'method_not_allowed',
      message: `This endpoint only supports the GET method. Found ${req.method}`
    })
    return
  }

  // Ensure that player query exists
  if (!req.query || !req.query.player) {
    res.status(400).json({
      code: 'missing_player',
      message: `Missing required query parameter: player`
    })
    return
  }

  const { player } = req.query

  // Check max-length of RSN
  if (player.length > 12) {
    res.status(400).json({
      code: 'player_too_long',
      message: 'Query parameter `player` must be 12 characters or fewer'
    })
    return
  }

  // Match alphanumeric, underscores, dashes, and spaces
  if (!player.match(/^[a-z0-9-_ ]+$/i)) {
    res.status(400).json({
      code: 'player_invalid',
      message:
        'Query parameter `player` must be alphanumeric, including underscore, dash, and spaces'
    })
    return
  }

  try {
    // Fetch, parse, and return the stats
    const raw = await getRawStats(player)
    const skills = parseRawStats(raw)

    // Cache the stats response for 60 seconds
    res.setHeader('Cache-Control', 'public, max-age=60')
    res.setHeader('Content-Type', 'application/json')
    res.status(200)
    res.json(skills)
  } catch (err) {
    const status = err.status || 500
    res.status(status).json({
      status,
      message: err.message || String(err)
    })
  }
}

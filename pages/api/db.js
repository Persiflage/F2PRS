import mysql from 'mysql'

import { getRawStats, parseRawStats } from '../../lib/rs-api'
const config = require('../../config')

function queryDB() {
  const connection = mysql.createConnection({
    host: config.dbHost,
    user: config.dbUsername,
    password: config.dbPassword,
    database: config.dbName
  })

  connection.connect()

  connection.query('SELECT * FROM stats', (err, rows, fields) => {
    if (err) throw err

    console.log(rows)
  })

  connection.end()
}

async function main() {
  const raw = await getRawStats('persiflage')
  const skills = parseRawStats(raw)
  // console.log(skills)

  return skills
}

export default async (req, res) => {
  res.json({
    success: true
  })
}

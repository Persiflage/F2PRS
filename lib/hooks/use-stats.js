import { useEffect, useState } from 'react'
import usePlayer from './use-player'

// Memory cache
const CACHE = new Map()

const useStats = () => {
  const [stats, setStats] = useState(undefined)
  const player = usePlayer()

  useEffect(() => {
    const getData = async () => {
      if (!player) {
        setStats(null)
        return
      }

      if (CACHE.has(player)) {
        setStats(CACHE.get(player))
        return
      }

      try {
        const data = await fetch(
          `/api/stats?player=${encodeURIComponent(player)}`,
          {
            method: 'GET'
          }
        )
        const json = await data.json()
        CACHE.set(player, json)
        setStats(json)
        return
      } catch (e) {
        console.error(e)
      }
    }

    getData()
  }, [player])

  return stats
}

export default useStats

import { useEffect } from 'react'

import useGame from '../../../lib/hooks/use-game'
import usePlayer from '../../../lib/hooks/use-player'
import useStats from '../../../lib/hooks/use-stats'
import validatePlayer from '../../../lib/validate-player'
import Error from '../../../components/layout/error'

const Page = () => {
  const game = useGame()
  const stats = useStats()
  const player = usePlayer()

  // useEffect(() => {
  //   if (!player) {
  //     return
  //   }

  //   const isBanned = async () => {
  //     const res = await fetch(`/api/ban?player${encodeURIComponent(player)}`)
  //     const json = await res.json()

  //     if (json && json.banned === true) {
  //       console.error('player is banned')
  //     }
  //   }

  //   isBanned()
  // }, [player])

  if (player && !validatePlayer(player)) {
    return <Error status={404} />
  }

  return (
    <div>
      {game}, {player}, {JSON.stringify(stats)}
    </div>
  )
}

export default Page

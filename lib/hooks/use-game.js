import Router, { useRouter } from 'next/router'

const games = ['rs3', 'osrs']

const useGame = () => {
  const { query } = useRouter()
  const { game } = query

  if (!game) {
    return undefined
  }

  // Invalid game, redirect home
  if (!games.includes(game)) {
    Router.push('/', '/')
  }

  return game
}

export default useGame

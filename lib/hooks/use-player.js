import { useRouter } from 'next/router'

const usePlayer = () => {
  const { query } = useRouter()
  const { player } = query

  if (!player) {
    return undefined
  }

  return player
}

export default usePlayer

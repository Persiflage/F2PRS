import { useEffect } from 'react'
import fetch from 'isomorphic-unfetch'

const Index = () => {
  useEffect(() => {
    const getData = async () => {
      const d = await fetch('/api/db')
      const j = await d.json()
      console.log(j)
    }

    getData()
  }, [])

  return (
    <div>F2PRS</div>
  )
}

export default Index

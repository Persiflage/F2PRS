import { useEffect } from 'react'
import Router from 'next/router'

const Page = () => {
  useEffect(() => {
    Router.push('/')
  }, [])

  return null
}

export default Page

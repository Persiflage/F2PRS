import React from 'react'
import Head from 'next/head'
import App from 'next/app'
import Page from '../components/layout/page'

class MyApp extends App {
  render() {
    const { Component, pageProps } = this.props

    return (
      <>
        <Head>
          <title>F2PRS</title>
          <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
          />
        </Head>

        <Page>
          <Component {...pageProps} />
        </Page>
      </>
    )
  }
}

export default MyApp

import React from 'react'
import Error from '../components/layout/error'

export default class E extends React.Component {
  static getInitialProps({ res, err }) {
    const statusCode = res ? res.statusCode : err ? err.statusCode : null
    return { statusCode }
  }

  render() {
    const { statusCode } = this.props

    return <Error status={statusCode} />
  }
}

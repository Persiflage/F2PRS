export default player => {
  if (!player) {
    return false
  }

  if (player.length > 12) {
    return false
  }

  if (!player.match(/^[a-z0-9-_ ]+$/i)) {
    return false
  }

  return true
}

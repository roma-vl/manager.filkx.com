export const roleBadgeClass = role => {
  switch (role.toLowerCase()) {
  case 'admin':
    return 'bg-red-600 text-white'
  case 'moderator':
    return 'bg-yellow-500 text-black'
  case 'user':
    return 'bg-green-600 text-white'
  default:
    return 'bg-gray-400 text-white'
  }
}

export const statusBadgeClass = status => {
  switch (status.toLowerCase()) {
  case 'active':
    return 'bg-green-500 text-white'
  case 'inactive':
    return 'bg-gray-400 text-white'
  case 'banned':
    return 'bg-red-700 text-white'
  default:
    return 'bg-blue-500 text-white'
  }
}

export function formatUtcDate(utcDate, locale = 'uk-UA', timeZone = 'Europe/Kyiv') {
  const date = new Date(utcDate + 'Z')
  return date.toLocaleString(locale, {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    timeZone,
  })
}


export const typeBadgeClass = type => {
  switch (type) {
    case 'none':
      return 'bg-gray-600 text-gray-100'
    case 'error':
      return 'bg-rose-700 text-rose-100'
    case 'bug':
      return 'bg-red-600 text-red-100'
    case 'feature':
      return 'bg-emerald-600 text-emerald-100'
    case 'task':
      return 'bg-sky-600 text-sky-100'
    case 'support':
      return 'bg-purple-600 text-purple-100'
    default:
      return 'bg-gray-600 text-gray-100'
  }
}

export const statusBadgeClass = status => {
  switch (status) {
    case 'new':
      return 'bg-green-800 text-green-100'
    case 'working':
      return 'bg-blue-600 text-blue-100'
    case 'help':
      return 'bg-yellow-400 text-yellow-900'
    case 'checking':
      return 'bg-purple-600 text-purple-100'
    case 'rejected':
      return 'bg-red-600 text-red-100'
    case 'done':
      return 'bg-green-600 text-green-100'
    default:
      return 'bg-gray-600 text-gray-100'
  }
}

export const formatPriority = priority => {
  switch (priority) {
    case 1:
      return 'LOW'
    case 2:
      return 'NORMAL'
    case 3:
      return 'FEATURE'
    case 4:
      return 'HIGH'
    case 5:
      return 'CRITICAL'
    case 6:
      return 'BLOCKER'
    default:
      return 'UNKNOWN'
  }
}
export const priorityBadgeClass = priority => {
  switch (priority) {
    case 1:
      return 'bg-green-600 text-green-100'
    case 2:
      return 'bg-blue-600 text-blue-100'
    case 3:
      return 'bg-yellow-500 text-yellow-900'
    case 4:
      return 'bg-orange-600 text-orange-100'
    case 5:
      return 'bg-red-700 text-red-100'
    case 6:
      return 'bg-purple-700 text-purple-100'
    default:
      return 'bg-gray-600 text-gray-100'
  }
}

export const formatStatus = status => {
  const map = {
    new: 'Нова',
    in_progress: 'В процесі',
    done: 'Завершена',
    failed: 'Провалена',
    help: 'Допомога',
  }

  return (map[status] ?? status).toUpperCase()
}

export const formatType = type => {
  const map = {
    none: 'Невизначеено',
    bug: 'баг',
    error: 'Помилка',
    feature: 'Функціонал',
    task: 'Задача',
  }

  return (map[type] ?? type).toUpperCase()
}

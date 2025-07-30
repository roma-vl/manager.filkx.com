import { ref } from 'vue'
import axios from 'axios'
import { Centrifuge } from 'centrifuge'

export function useCentrifugo() {
  const centrifuge = ref(null)
  const subscriptions = new Map()

  async function init(url = 'ws://localhost:8083/connection/websocket') {
    try {
      const { data } = await axios.get('/api/centrifugo/token')
      if (!data.token) throw new Error('No token received')

      centrifuge.value = new Centrifuge(url, {
        token: data.token,
        // debug: true,
      })

      // centrifuge.value.on('connecting', (ctx) => {
      //     console.log(`connecting: ${ctx.code}, ${ctx.reason}`)
      // })
      //
      // centrifuge.value.on('connected', (ctx) => {
      //     console.log(`connected over ${ctx.transport}`)
      // })
      //
      // centrifuge.value.on('disconnected', (ctx) => {
      //     console.log(`disconnected: ${ctx.code}, ${ctx.reason}`)
      // })

      centrifuge.value.connect()
    } catch (error) {
      console.error('Centrifugo init error:', error)
      throw error
    }
  }

  function subscribe(channelName, handlers) {
    if (!centrifuge.value) {
      throw new Error('Centrifugo client is not initialized')
    }

    if (subscriptions.has(channelName)) {
      console.warn(`Already subscribed to ${channelName}`)
      return subscriptions.get(channelName)
    }

    const sub = centrifuge.value.newSubscription(channelName)

    if (handlers.publication) sub.on('publication', handlers.publication)
    if (handlers.subscribing) sub.on('subscribing', handlers.subscribing)
    if (handlers.subscribed) sub.on('subscribed', handlers.subscribed)
    if (handlers.unsubscribed) sub.on('unsubscribed', handlers.unsubscribed)
    if (handlers.error) sub.on('error', handlers.error)

    sub.subscribe()
    subscriptions.set(channelName, sub)
    return sub
  }

  function unsubscribe(channelName) {
    const sub = subscriptions.get(channelName)
    if (sub) {
      sub.unsubscribe()
      subscriptions.delete(channelName)
    }
  }

  function disconnect() {
    centrifuge.value?.disconnect()
    subscriptions.clear()
  }

  return {
    centrifuge,
    init,
    subscribe,
    unsubscribe,
    disconnect,
  }
}

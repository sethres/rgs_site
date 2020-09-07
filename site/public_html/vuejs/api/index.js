export default {
  API: class API {
    constructor (url, settings, vue, outsideAPI) {
      this.url = '//' + window.location.hostname + '/' + (!outsideAPI ? 'api/' : '') + url

      this.settings = {
        alert: null, // function to handle alerts
        data: {}, // data to be posted
        error: null,
        errorMessage: '', // error message
        requestSettings: { // request settings for heaeders/response type
          headers: {}
        },
        success: null // success callback function
      }

      this.vue = vue
      this.settings = this.mergeDeep(this.settings, settings)
    }

    isObject (item) {
      return (item && typeof item === 'object' && !Array.isArray(item))
    }

    // https://stackoverflow.com/questions/27936772/how-to-deep-merge-instead-of-shallow-merge
    mergeDeep (target, ...sources) {
      if (!sources.length) {
        return target
      }
      const source = sources.shift()

      if (this.isObject(target) && this.isObject(source)) {
        for (const key in source) {
          if (this.isObject(source[key]) && key !== 'validateElem' && key !== 'data') {
            if (!target[key]) {
              Object.assign(target, { [key]: {} })
            }
            this.mergeDeep(target[key], source[key])
          } else {
            Object.assign(target, { [key]: source[key] })
          }
        }
      }

      return this.mergeDeep(target, ...sources)
    }

    alerts (alerts) {
      if (typeof this.settings.alert === 'function') {
        this.settings.alert(alerts)
      }
    }

    success (data) {
      if (typeof this.settings.success === 'function') {
        this.settings.success(data)
      }
    }

    response (response) {
      if (response.Data !== undefined || response.Alerts !== undefined) {
        if (response.Alerts !== undefined) {
          this.alerts(response.Alerts)
        } else {
          this.success(response.Data)
        }
      } else {
        this.success(response)
      }
    }

    error (err) {
      if (this.settings.errorMessage !== '') {
        this.vue.$store.commit('SET_TOAST_MESSAGE', { message: this.settings.errorMessage })
      }

      if (typeof this.settings.error === 'function') {
        this.settings.error()
      }
    }

    get () {
      window.fetchival(this.url, this.settings.requestSettings
      ).get(
      ).then(response => {
        this.response(response)
      }).catch(err => {
        this.error(err)
      })
    }
  },

  get (url, settings, vue, outsideAPI = false) {
    let API = new this.API(url, settings, vue, outsideAPI)
    API.get()
  }
}

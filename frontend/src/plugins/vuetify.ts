import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { pt } from 'vuetify/locale'

export default createVuetify({
  components,
  directives,
  locale: {
    locale: 'pt',
    messages: { pt },
  },
  theme: {
    defaultTheme: 'light',
    themes: {
      light: {
        colors: {
          primary: '#1565C0',
          secondary: '#0277BD',
        },
      },
    },
  },
})

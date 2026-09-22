import { createI18n } from 'vue-i18n'
import bn from '@/locales/bn.json'
import en from '@/locales/en.json'

// Detect saved/browser locale, fallback to bn
function getInitialLocale(): string {
  const saved = localStorage.getItem('locale')
  if (saved && ['bn', 'en'].includes(saved)) return saved

  const browserLang = navigator.language.slice(0, 2)
  return ['bn', 'en'].includes(browserLang) ? browserLang : 'bn'
}

const i18n = createI18n({
  legacy: false,        // required for Composition API (useI18n)
  locale: getInitialLocale(),
  fallbackLocale: 'bn',
  messages: {
    bn,
    en,
  },
})

export default i18n
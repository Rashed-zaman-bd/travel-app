import { useI18n } from 'vue-i18n'

export function useAppLocale() {
  return useI18n({ useScope: 'global' })
}
<template>
  <div class="h-10 w-full bg-blue-400">
    <div class="mx-auto flex h-full max-w-7xl items-center justify-center px-4">
      <span class="text-sm text-white">Ad Banner</span>
    </div>
  </div>

  <header class="sticky top-0 z-50 w-full bg-white shadow-md">
    <nav class="mx-auto grid max-w-7xl grid-cols-3 items-center px-4 py-4 sm:flex sm:justify-between sm:px-6 sm:py-6 lg:px-8">

      <div class="flex items-center sm:hidden">
        <button
          type="button"
          class="flex h-10 w-10 items-center justify-center rounded-lg text-gray-700 hover:bg-gray-100"
          aria-label="Toggle Navigation"
          @click="mobileMenuOpen = !mobileMenuOpen"
        >
          <svg
            v-if="!mobileMenuOpen"
            class="h-6 w-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg
            v-else
            class="h-6 w-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <div class="flex shrink-0 items-center justify-center sm:justify-start">
        <a href="/" class="block">
          <img
            src="/images/travel-logo.png"
            alt="Travel Logo"
            class="h-5 w-auto object-contain sm:h-6"
          />
        </a>
      </div>

      <div class="flex items-center justify-end sm:flex-1 sm:justify-center">
        <ul class="hidden items-center gap-5 sm:flex lg:gap-7 xl:gap-8">
          <li>
            <a href="#" class="whitespace-nowrap text-base font-medium text-gray-700 transition hover:text-blue-500">
              Flight
            </a>
          </li>
          <li>
            <a href="#" class="whitespace-nowrap text-base font-medium text-gray-700 transition hover:text-blue-500">
              Hotel
            </a>
          </li>
          <li>
            <a href="#" class="whitespace-nowrap text-base font-medium text-gray-700 transition hover:text-blue-500">
              Holiday
            </a>
          </li>
          <li>
            <a href="#" class="whitespace-nowrap text-base font-medium text-gray-700 transition hover:text-blue-500">
              Shop
            </a>
          </li>
          <li>
            <a href="#" class="whitespace-nowrap text-base font-medium text-gray-700 transition hover:text-blue-500">
              Package
            </a>
          </li>

          <li
            class="group relative"
            @mouseenter="visaOpen = true"
            @mouseleave="visaOpen = false"
          >
            <button
              type="button"
              class="flex items-center gap-1 whitespace-nowrap text-base font-medium text-gray-700 transition hover:text-blue-500"
              aria-haspopup="true"
              :aria-expanded="visaOpen"
            >
              Visa
              <svg
                class="h-4 w-4 transition-transform duration-200"
                :class="{ 'rotate-180': visaOpen }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
              </svg>
            </button>

            <div
              v-show="visaOpen"
              class="absolute left-1/2 top-full z-[9999] pt-6 -translate-x-1/2 transition-all duration-300 ease-out"
              :class="visaOpen ? 'opacity-100 translate-y-0 pointer-events-auto' : 'opacity-0 -translate-y-2 pointer-events-none'"
            >
              <div class="w-48 overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black/5">
                <a href="#" class="block px-5 py-3 text-base font-medium text-gray-500 transition hover:bg-blue-50 hover:text-blue-500">Tourist Visa</a>
                <a href="#" class="block px-5 py-3 text-base font-medium text-gray-500 transition hover:bg-blue-50 hover:text-blue-500">Student Visa</a>
                <a href="#" class="block px-5 py-3 text-base font-medium text-gray-500 transition hover:bg-blue-50 hover:text-blue-500">Business Visa</a>
                <a href="#" class="block px-5 py-3 text-base font-medium text-gray-500 transition hover:bg-blue-50 hover:text-blue-500">Work Visa</a>
                <a href="#" class="block px-5 py-3 text-base font-medium text-gray-500 transition hover:bg-blue-50 hover:text-blue-500">Visa Assistance</a>
              </div>
            </div>
          </li>

          <li>
            <a href="#" class="whitespace-nowrap text-base font-medium text-gray-700 transition hover:text-blue-500">
              Car rent
            </a>
          </li>

          <li
            class="group relative"
            @mouseenter="othersOpen = true"
            @mouseleave="othersOpen = false"
          >
            <button
              type="button"
              class="flex items-center gap-1 whitespace-nowrap text-base font-medium text-gray-700 transition hover:text-blue-500"
              aria-haspopup="true"
              :aria-expanded="othersOpen"
            >
              Others
              <svg
                class="h-4 w-4 transition-transform duration-200"
                :class="{ 'rotate-180': othersOpen }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
              </svg>
            </button>

            <div
              v-show="othersOpen"
              class="absolute left-1/2 top-full z-[9999] pt-6 -translate-x-1/2 transition-all duration-300 ease-out"
              :class="othersOpen ? 'opacity-100 translate-y-0 pointer-events-auto' : 'opacity-0 -translate-y-2 pointer-events-none'"
            >
              <div class="w-48 overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black/5">
                <a href="#" class="block px-5 py-3 text-base font-medium text-gray-500 transition hover:bg-blue-50 hover:text-blue-500">About</a>
                <a href="#" class="block px-5 py-3 text-base font-medium text-gray-500 transition hover:bg-blue-50 hover:text-blue-500">FAQ & Support</a>
                <a href="#" class="block px-5 py-3 text-base font-medium text-gray-500 transition hover:bg-blue-50 hover:text-blue-500">Tour Guide</a>
                <a href="#" class="block px-5 py-3 text-base font-medium text-gray-500 transition hover:bg-blue-50 hover:text-blue-500">Gift Card</a>
                <a href="#" class="block px-5 py-3 text-base font-medium text-gray-500 transition hover:bg-blue-50 hover:text-blue-500">Why Traviasa?</a>
              </div>
            </div>
          </li>
        </ul>
      </div>

      <div class="hidden sm:flex items-center gap-4">
        <UserAccount/>
      </div>
    </nav>
    
    <Transition name="expand">
      <div
        v-if="mobileMenuOpen"
        class="overflow-hidden border-t border-gray-100 bg-white px-4 pb-6 pt-4 sm:hidden"
      >
        <ul class="flex flex-col gap-2">
          <li>
            <a href="#" class="block rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600">Flight</a>
          </li>
          <li>
            <a href="#" class="block rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600">Hotel</a>
          </li>
          <li>
            <a href="#" class="block rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600">Holiday</a>
          </li>
          <li>
            <a href="#" class="block rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600">Shop</a>
          </li>
          <li>
            <a href="#" class="block rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600">Package</a>
          </li>

          <li>
            <button
              type="button"
              class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600"
              @click="mobileVisaOpen = !mobileVisaOpen"
            >
              <span>Visa</span>
              <svg
                class="h-4 w-4 transition-transform duration-200"
                :class="{ 'rotate-180': mobileVisaOpen }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <Transition name="expand">
              <div v-if="mobileVisaOpen" class="ml-4 overflow-hidden space-y-1 pt-1">
                <a href="#" class="block rounded-md px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-blue-600">Visa Application</a>
                <a href="#" class="block rounded-md px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-blue-600">Visa Guide</a>
                <a href="#" class="block rounded-md px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-blue-600">Student Visa processing</a>
                <a href="#" class="block rounded-md px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-blue-600">On Arrival</a>
                <a href="#" class="block rounded-md px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-blue-600">Transit Visa</a>
              </div>
            </Transition>
          </li>

          <li>
            <a href="#" class="block rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600">Car Rent</a>
          </li>

          <li>
            <button
              type="button"
              class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600"
              @click="mobileOthersOpen = !mobileOthersOpen"
            >
              <span>Others</span>
              <svg
                class="h-4 w-4 transition-transform duration-200"
                :class="{ 'rotate-180': mobileOthersOpen }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <Transition name="expand">
              <div v-if="mobileOthersOpen" class="ml-4 overflow-hidden space-y-1 pt-1">
                <a href="#" class="block rounded-md px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-blue-600">About us</a>
                <a href="#" class="block rounded-md px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-blue-600">Blog</a>
                <a href="#" class="block rounded-md px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-blue-600">Tour Guide</a>
                <a href="#" class="block rounded-md px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-blue-600">Gift Card</a>
                <a href="#" class="block rounded-md px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-blue-600">FAQ & Support</a>
              </div>
            </Transition>
          </li>
          
          <li class="pt-2 border-t border-gray-100">
            <UserAccount/>
          </li>
        </ul>
      </div>
    </Transition>
  </header>
</template>

<script setup lang="ts">
import UserAccount from '@/components/UserAccount.vue'
import { ref } from 'vue'

const visaOpen = ref(false)
const othersOpen = ref(false)

const mobileMenuOpen = ref(false)
const mobileVisaOpen = ref(false)
const mobileOthersOpen = ref(false)
</script>

<style scoped>
/* Smooth Expand/Collapse Animations */
.expand-enter-active,
.expand-leave-active {
  transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
  max-height: 500px;
}

.expand-enter-from,
.expand-leave-to {
  max-height: 0;
  opacity: 0;
}
</style>
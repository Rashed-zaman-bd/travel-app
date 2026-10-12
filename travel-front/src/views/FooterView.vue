```vue
<template>
  <footer class="border-t border-gray-200 bg-white">
    <div
      class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-5 py-8 sm:grid-cols-2 lg:grid-cols-3 lg:gap-10"
    >
      <!-- Contact Us -->
      <div>
        <h3 class="mb-4 text-lg font-bold text-gray-900">
          Contact Us
        </h3>

        <div class="space-y-1.5 text-sm text-gray-700">
          <p>
            Email:
            <a
              href="#"
              class="text-blue-600 hover:underline"
            >
              info@jatrajai.com
            </a>
          </p>

          <p>
            Hotline:
            <a
              href="#"
              class="text-blue-600 hover:underline"
            >
              +88 01948 969039
            </a>
          </p>

          <p>
            WhatsApp:
            <a
              href="https://wa.me/"
              target="_blank"
              rel="noopener noreferrer"
              class="text-blue-600 hover:underline"
            >
              Message us
            </a>
          </p>
        </div>

        <!-- Social Media -->
        <div class="mt-5 flex items-center gap-5 text-gray-400">
          <!-- Facebook -->
            <a href="https://www.facebook.com/profile.php?id=61595214273096" aria-label="Facebook" class="text-gray-400 hover:text-blue-600">
            <font-awesome-icon :icon="['fab', 'facebook-f']" />
            </a>

            <!-- Youtube -->
            <a href="#" aria-label="Youtub" class="text-gray-400 hover:text-red-500">
            <font-awesome-icon :icon="['fab', 'youtube']" />
            </a>

            <!-- WhatsApp -->
            <a href="#" aria-label="WhatsApp" class="text-gray-400 hover:text-green-600">
            <font-awesome-icon :icon="['fab', 'whatsapp']" />
            </a>

            <!-- Instagram -->
            <a href="#" aria-label="Instagram" class="text-gray-400 hover:text-pink-500">
            <font-awesome-icon :icon="['fab', 'instagram']" />
            </a>

            <!-- Twitter -->
            <a href="#" aria-label="Twiter" class="text-gray-400 hover:text-blue-400">
            <font-awesome-icon :icon="['fab', 'twitter']" />
            </a>

        </div>
      </div>

      <!-- Address Office -->
      <div>
        <h3 class="mb-4 text-lg font-bold text-gray-900">
          Address
        </h3>

        <div class="text-sm leading-5 text-gray-700">
          <p>House-12, Road-02</p>
          <p>Sector-6, Uttara</p>
          <p>Dhaka</p>
        </div>

            <a
            href="https://www.google.com/maps/search/?api=1&query=Future+Town+Housing%2C+Road+No+1%2C+Dhaka+1230"
            target="_blank"
            rel="noopener noreferrer"
            class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-gray-900 transition hover:text-orange-500"
            >
            <font-awesome-icon
                :icon="['fas', 'location-dot']"
                class="text-lg text-orange-500"
            />
                View Map
            </a>

      </div>

      <div>
        <h3 class="mb-4 text-lg font-bold text-gray-900">
          Discover
        </h3>

        <div class="text-sm leading-5 text-gray-700">
          <p>FAQ</p>
          <p>Travel articles</p>
          <p>Why are you travel with JatraJai?</p>
        </div>

      </div>

    </div>

    <!-- Bottom Border -->
    <div class="mx-auto max-w-7xl px-5">
      <div class="border-t border-gray-100"></div>
    </div>

    <div class="border-t border-gray-200 bg-white px-4 py-5">
        <div
            class="mx-auto flex max-w-7xl flex-col items-center justify-center gap-2 text-center text-sm text-gray-500 sm:flex-row sm:text-left"
            >
            <p>
                Copyright © {{ new Date().getFullYear() }}.
                <a
                href="#"
                target="_blank"
                rel="noopener noreferrer"
                class="font-semibold text-gray-800 transition hover:text-orange-500"
                >
                JatraJai
                </a>.
                All rights reserved.
            </p>

        </div>
    </div>
  </footer>
</template>
```
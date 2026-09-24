```vue
<template>
  <section class="w-full border-y border-gray-100 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div
        class="flex flex-col gap-8 py-8 lg:flex-row lg:items-center lg:gap-0"
      >

        <!-- Left label -->
        <div
          class="flex items-center justify-center lg:w-40 lg:shrink-0 lg:justify-start lg:border-r lg:border-gray-200 lg:pr-6"
        >
          <span
            class="text-sm font-bold tracking-wide text-slate-600"
          >
            HOW IT WORKS
          </span>
        </div>

        <!-- Steps -->
        <div
          class="grid w-full flex-1 grid-cols-1 gap-10 sm:grid-cols-3 lg:pl-10"
        >
          <div
            v-for="step in steps"
            :key="step.number"
            class="flex flex-col items-center gap-2 text-center sm:items-start sm:text-left"
          >

            <!-- Icon + Step -->
            <div
              class="flex flex-col items-center gap-2 sm:flex-row"
            >
              <font-awesome-icon
                :icon="step.icon"
                class="text-4xl text-amber-400"
              />

              <span
                class="text-base font-bold tracking-wide text-amber-400"
              >
                STEP {{ step.number }}
              </span>
            </div>

            <!-- Title -->
            <h3
              class="mt-1 text-base font-bold leading-snug text-slate-800"
            >
              {{ step.title }}
            </h3>

            <!-- Description -->
            <p
              class="max-w-xs text-sm leading-relaxed text-slate-500"
            >
              {{ step.description }}
            </p>

          </div>
        </div>

      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
interface Step {
  number: number
  title: string
  description: string
  icon: [string, string]
}

const steps: Step[] = [
  {
    number: 1,
    title: 'Tell us what kind of trip you want',
    description: 'Complete our quick travel quiz',
    icon: ['far', 'circle-user'],
  },
  {
    number: 2,
    title: 'Chat with a local travel specialist',
    description: 'Work out the details together',
    icon: ['fas', 'comments'],
  },
  {
    number: 3,
    title: 'Securely book your trip',
    description: '100% satisfaction guaranteed',
    icon: ['fas', 'credit-card'],
  },
]
</script>
```

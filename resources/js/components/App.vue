<template>
  <!-- Fixed background layer -->
  <div class="fixed inset-0 bg-repeat -z-10"
      style="background-image: url('data:image/svg+xml,%3Csvg width=\'70\' height=\'70\' viewBox=\'0 0 70 70\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23d920b5\' fill-opacity=\'0.74\' fill-rule=\'evenodd\'%3E%3Cpath d=\'M0 0h35v35H0V0zm5 5h25v25H5V5zm5 5h15v15H10V10zm5 5h5v5h-5v-5zM40 5h25v25H40V5zm5 5h15v15H45V10zm5 5h5v5h-5v-5zM70 35H35v35h35V35zm-5 5H40v25h25V40zm-5 5H45v15h15V45zm-5 5h-5v5h5v-5zM30 40H5v25h25V40zm-5 5H10v15h15V45zm-5 5h-5v5h5v-5z\'/%3E%3C/g%3E%3C/svg%3E');">
  </div>

  <!-- Fixed Header -->
  <header class="fixed top-0 left-0 w-full py-4 bg-white shadow text-center z-20">
    <h1 class="text-2xl font-bold text-gray-800">🐾 Paws</h1>
  </header>

  <!-- Main Content (with padding to prevent hiding behind fixed header) -->
  <div class="pt-24 flex flex-col items-center justify-center min-h-screen relative z-10">

    <!-- Main Content -->
    <div>
      <!-- Stacked Cards -->
      <div v-if="current < cats.length" class="relative w-72 h-96">
        <!-- Show up to 3 stacked cards -->
        <div
          v-for="(cat, i) in visibleStack"
          :key="i"
          class="absolute w-full h-full rounded-xl shadow-lg overflow-hidden transition-transform duration-300"
          :style="getCardStyle(i)"
        >
          <!-- Top Card = Interactive -->
          <img
            v-if="i === 0"
            :src="cat"
            class="w-full h-full object-cover"
            @mousedown="startDrag"
            @mousemove="onDrag"
            @mouseup="endDrag"
            @mouseleave="endDrag"
            @touchstart="startDrag"
            @touchmove="onDrag"
            @touchend="endDrag"
            :style="{
              transform: `translate(${offsetX}px, ${offsetY}px) rotate(${rotation}deg)`,
              transition: isDragging ? 'none' : 'transform 0.3s ease',
            }"
          />
          <!-- Other stacked cards -->
          <img v-else :src="cat" class="w-full h-full object-cover" />
        </div>

        <!-- Floating ❤️ / 💔 Emojis -->
        <div class="absolute inset-0 pointer-events-none z-30">
          <span
            v-for="(emoji, index) in floatingEmojis"
            :key="index"
            :style="emoji.style"
            class="absolute text-3xl animate-float"
          >
            {{ emoji.icon }}
          </span>
        </div>

        <!-- Desktop Buttons -->
        <div class="absolute inset-0 z-40 hidden md:flex items-end justify-between px-4 pb-6">
          <button @click="dislike" class="bg-red-500 text-white rounded-full p-4">👎</button>
          <button @click="like" class="bg-green-500 text-white rounded-full p-4">👍</button>
        </div>
      </div>

      <!-- Liked Results -->
      <div v-if="current >= cats.length" class="text-center mt-6">
        <h2 class="text-xl font-bold mb-2">You liked {{ liked.length }} cats!</h2>
        <div v-if="liked.length === 0" class="flex flex-col items-center">
          <img
            src="https://cataas.com/cat/cute"
            alt="Swinging Cat"
            class="w-32 h-32 object-cover rounded-md animate-swing"
          />
          <p class="text-xl font-bold mb-2">No cats liked? How dare you!</p>
        </div>
        <div v-else class="grid grid-cols-2 gap-2">
          <img
            v-for="cat in liked"
            :key="cat"
            :src="cat"
            class="w-32 h-32 object-cover rounded-md"
          />
        </div>
        <button @click="restart" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">
          Try Again
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, onMounted, watch, computed } from 'vue'
  import confetti from 'canvas-confetti'

  // State
  const cats = ref([])
  const liked = ref([])
  const current = ref(0)
  const floatingEmojis = ref([])

  // Drag state
  const offsetX = ref(0)
  const offsetY = ref(0)
  const rotation = ref(0)
  const isDragging = ref(false)
  let startX = 0
  let startY = 0

  // Load cats
  const fetchCats = async () => {
    cats.value = Array.from({ length: 10 }, (_, i) => `https://cataas.com/cat?unique=${Date.now() + i}`)
  }

  // Floating hearts/heartbreaks
  const generateFloatingEmojis = (icon) => {
    for (let i = 0; i < 6; i++) {
      floatingEmojis.value.push({
        icon,
        style: {
          left: Math.random() * 100 + '%',
          top: '80%',
          animationDuration: `${1 + Math.random()}s`,
          opacity: 1,
        },
      })
    }
    setTimeout(() => {
      floatingEmojis.value = []
    }, 1500)
  }

  // Like / Dislike
  const like = () => {
    liked.value.push(cats.value[current.value])
    current.value++
    generateFloatingEmojis('❤️')
    resetDrag()
  }

  const dislike = () => {
    current.value++
    generateFloatingEmojis('💔')
    resetDrag()
  }

  const restart = () => {
    current.value = 0
    liked.value = []
    fetchCats()
  }

  // Handle drag gestures
  const startDrag = (e) => {
    isDragging.value = true
    const touch = e.touches?.[0] || e
    startX = touch.clientX
    startY = touch.clientY
  }

  const onDrag = (e) => {
    if (!isDragging.value) return
    const touch = e.touches?.[0] || e
    offsetX.value = touch.clientX - startX
    offsetY.value = touch.clientY - startY
    rotation.value = offsetX.value / 10
  }

  const endDrag = () => {
    if (!isDragging.value) return
    isDragging.value = false
    const threshold = 100
    if (offsetX.value > threshold) {
      like()
    } else if (offsetX.value < -threshold) {
      dislike()
    } else {
      resetDrag()
    }
  }

  const resetDrag = () => {
    offsetX.value = 0
    offsetY.value = 0
    rotation.value = 0
  }

  // Compute visible stacked cards
  const visibleStack = computed(() => {
    return cats.value.slice(current.value, current.value + 3)
  })

  // Style each card in the stack
  const getCardStyle = (i) => {
    const z = 10 - i
    if (i === 0) return { zIndex: z }
    const scale = 1 - i * 0.05
    const translateY = i * 10
    return {
      transform: `scale(${scale}) translateY(${translateY}px)`,
      zIndex: z,
    }
  }

  // Confetti on complete
  watch(current, (val) => {
    if (val >= cats.value.length) {
      confetti({
        particleCount: 150,
        spread: 70,
        origin: { y: 0.6 },
      })
    }
  })

  onMounted(fetchCats)
</script>

<style scoped>
@keyframes float {
  0% {
    transform: translateY(0) scale(1);
    opacity: 1;
  }
  100% {
    transform: translateY(-150px) scale(1.5);
    opacity: 0;
  }
}

.animate-float {
  animation-name: float;
  animation-timing-function: ease-out;
  animation-fill-mode: forwards;
  position: absolute;
}
@keyframes swing {
  0%   { transform: rotate(0deg); }
  25%  { transform: rotate(10deg); }
  50%  { transform: rotate(-10deg); }
  75%  { transform: rotate(6deg); }
  100% { transform: rotate(0deg); }
}

.animate-swing {
  animation: swing 0.6s ease-in-out 2;
}

</style>

<template>
  <nav v-if="pages.length > 1" aria-label="Pagination">
    <ul class="pagination justify-content-center">
      <li :class="['page-item', { disabled: !store.canPrev }]">
        <button class="page-link" @click="prev" :disabled="!store.canPrev">
          &laquo;
        </button>
      </li>

      <li
        v-for="p in pages"
        :key="p"
        :class="['page-item', { active: p === store.pagination.currentPage }]"
      >
        <button class="page-link" @click="go(p)">{{ p }}</button>
      </li>

      <li :class="['page-item', { disabled: !store.canNext }]">
        <button class="page-link" @click="next" :disabled="!store.canNext">
          &raquo;
        </button>
      </li>
    </ul>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useTaskStore } from '@/stores/taskStore'

const store = useTaskStore()

const pages = computed(() => {
  const { currentPage, lastPage } = store.pagination
  const from = Math.max(1, currentPage - 2)
  const to   = Math.min(lastPage, currentPage + 2)
  return Array.from({ length: to - from + 1 }, (_, i) => from + i)
})

function go (p)   { if (p !== store.pagination.currentPage) store.changePage(p) }
function prev ()  { if (store.canPrev) store.changePage(store.pagination.currentPage - 1) }
function next ()  { if (store.canNext) store.changePage(store.pagination.currentPage + 1) }
</script> 
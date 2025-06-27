<template>
  <table class="table table-hover">
    <thead>
      <tr>
        <th>Tytuł</th>
        <th>Status</th>
        <th>Priorytet</th>
        <th class="text-end">Akcje</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="task in tasks" :key="task.id">
        <td>{{ task.title }}</td>
        <td>
          <span :class="statusClass(task.status)">
            {{ task.status }}
          </span>
        </td>
        <td>{{ task.priority }}</td>
        <td class="text-end">
          <button
            class="btn btn-sm btn-outline-primary me-2"
            @click="$emit('edit-task', task)"
          >
            <i class="bi bi-pencil"></i>
          </button>
          <button
            class="btn btn-sm btn-outline-danger"
            @click="$emit('delete-task', task)"
          >
            <i class="bi bi-trash"></i>
          </button>
        </td>
      </tr>
      <tr v-if="!tasks.length">
        <td colspan="4" class="text-center text-muted py-4">
          Brak zadań do wyświetlenia
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script setup>
import { computed } from 'vue'

defineProps({
  tasks: {
    type: Array,
    required: true
  }
})

/**
 * Zwraca klasę CSS zależnie od statusu.
 */
function statusClass (status) {
  switch (status) {
    case 'completed':
      return 'badge bg-success'
    case 'in-progress':
      return 'badge bg-warning text-dark'
    default: // pending
      return 'badge bg-secondary'
  }
}
</script> 
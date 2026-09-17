<script setup>
import { RouterView } from "vue-router";
</script>

<template>
  <div class="app-root">
    <RouterView />
  </div>
</template>

<style>
@import url("https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,300;0,400;0,600;0,700;0,800;1,700&family=Moul&display=swap");

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body,
html {
  background-color: #0c1017;
  color: #f1f5f9;
  font-family: "Kantumruy Pro", sans-serif;
  -webkit-font-smoothing: antialiased;
}

.app-root {
  min-height: 100vh;
  width: 100%;
  background-color: #0c1017;
}
</style>

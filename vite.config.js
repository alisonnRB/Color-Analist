// vite.config.js
export default {
    build: {
      outDir: 'dist',
      emptyOutDir: true,
      target: 'esnext',
      assetsDir: '',
      rollupOptions: {
        input: {
          main: './resources/main.js'
        }
      }
    }
  }
  
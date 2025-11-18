<style>
    :root {
        font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        color: #1b1b18;
        background-color: #fdfdfc;
    }

    body {
        margin: 0;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        text-align: center;
    }

    .fallback {
        max-width: 420px;
        border-radius: 24px;
        padding: 2.5rem;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
        background: rgba(255, 255, 255, 0.9);
    }

    .fallback h1 {
        font-size: clamp(1.8rem, 2vw, 2.2rem);
        margin-bottom: 0.75rem;
    }

    .fallback p {
        font-size: 1rem;
        color: rgba(0, 0, 0, 0.65);
        line-height: 1.6;
    }
</style>

<div class="fallback">
    <h1>Сборка фронтенда не запущена</h1>
    <p>
        Запустите <strong>npm run dev</strong>, чтобы увидеть страницу авторизации.
        Этот блок показывается только в режиме без Vite.
    </p>
</div>


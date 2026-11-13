<style>
    .suket-tl { position: relative; min-width: 168px; padding: 2px 2px 0; }
    .suket-tl--lg { min-width: 0; padding: 8px 8px 4px; }
    .suket-tl__bar {
        position: absolute;
        left: 10%;
        right: 10%;
        top: 7px;
        height: 3px;
        background: #e0e0e0;
        border-radius: 2px;
        z-index: 0;
        overflow: hidden;
    }
    .suket-tl--lg .suket-tl__bar { top: 12px; height: 4px; left: 8%; right: 8%; }
    .suket-tl__fill { height: 100%; background: #2ca87f; border-radius: 2px; }
    .suket-tl__steps {
        display: flex;
        justify-content: space-between;
        list-style: none;
        margin: 0;
        padding: 0;
        position: relative;
        z-index: 1;
    }
    .suket-tl__step {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        min-width: 0;
    }
    .suket-tl__dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #cfd8dc;
        display: block;
        box-sizing: border-box;
    }
    .suket-tl--lg .suket-tl__dot { width: 20px; height: 20px; border-width: 3px; }
    .suket-tl__step.is-done .suket-tl__dot,
    .suket-tl__step.is-current .suket-tl__dot {
        background: #2ca87f;
        border-color: #2ca87f;
    }
    .suket-tl__step.is-current .suket-tl__dot {
        box-shadow: 0 0 0 3px rgba(44, 168, 127, 0.28);
    }
    .suket-tl__label {
        margin-top: 4px;
        font-size: 10px;
        line-height: 1.2;
        color: #90a4ae;
        white-space: nowrap;
    }
    .suket-tl--lg .suket-tl__label { font-size: 13px; font-weight: 600; color: #546e7a; }
    .suket-tl__step.is-done .suket-tl__label,
    .suket-tl__step.is-current .suket-tl__label { color: #2ca87f; font-weight: 600; }
    .suket-tl__hint { display: block; font-size: 11px; color: #90a4ae; margin-top: 2px; line-height: 1.3; }
    .suket-tl__step.is-current .suket-tl__hint { color: #37474f; }
</style>

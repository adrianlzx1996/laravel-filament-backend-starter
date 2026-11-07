import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Backend/**/*.php',
        './resources/views/filament/backend/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}

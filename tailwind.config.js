import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                heading: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#2F5D3A',
                    hover: '#254A2F',
                    light: '#EAF3EC',
                    dark: '#164525',
                },
                accent: {
                    DEFAULT: '#E6B73C',
                    hover: '#D4A32B',
                    light: '#FBF1D6',
                },
                canvas: '#F2F5EF',
                surface: '#FFFFFF',
                'border-soft': '#E3E8E1',
                ink: {
                    DEFAULT: '#1E2A22',
                    muted: '#66736A',
                },
                danger: {
                    DEFAULT: '#DC2626',
                    hover: '#B91C1C',
                    soft: '#FEE2E2',
                    ink: '#991B1B',
                },
                success: {
                    DEFAULT: '#16A34A',
                    hover: '#15803D',
                    soft: '#DCFCE7',
                    ink: '#14532D',
                },
                error: '#DC2626',
                'primary-soft': '#EAF3EC',
                status: {
                    menunggu: {
                        bg: '#FEF3C7',
                        text: '#B7791F',
                        dot: '#D97706',
                    },
                    kurang_berkas: {
                        bg: '#FFEDD5',
                        text: '#C2410C',
                        dot: '#EA580C',
                    },
                    diproses: {
                        bg: '#DBEAFE',
                        text: '#1D4ED8',
                        dot: '#2563EB',
                    },
                    menunggu_ttd: {
                        bg: '#EDE9FE',
                        text: '#6D28D9',
                        dot: '#7C3AED',
                    },
                    selesai: {
                        bg: '#DCFCE7',
                        text: '#15803D',
                        dot: '#16A34A',
                    },
                    ditolak: {
                        bg: '#FEE2E2',
                        text: '#B91C1C',
                        dot: '#DC2626',
                    },
                },
            },
            borderRadius: {
                card: '24px',
                btn: '14px',
            },
            boxShadow: {
                xs: '0 1px 2px 0 rgba(30, 42, 34, 0.05)',
                card: '0 8px 24px -4px rgba(30, 42, 34, 0.04), 0 2px 6px -1px rgba(30, 42, 34, 0.02)',
                featured: '0 12px 28px -6px rgba(47, 93, 58, 0.25)',
                modal: '0 20px 32px -8px rgba(30, 42, 34, 0.12), 0 4px 12px rgba(30, 42, 34, 0.04)',
            },
        },
    },

    plugins: [forms],
};

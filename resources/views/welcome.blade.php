<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UAS - Parfum Store API</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fira-code:400,500,600,700|inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS (via CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['Fira Code', 'monospace'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        glow: {
                            '0%': { boxShadow: '0 0 10px rgba(14, 165, 233, 0.2), 0 0 20px rgba(14, 165, 233, 0.1)' },
                            '100%': { boxShadow: '0 0 20px rgba(14, 165, 233, 0.6), 0 0 30px rgba(14, 165, 233, 0.3)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #030712;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,0.15) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,0.15) 0, transparent 50%);
            background-attachment: fixed;
            color: #f3f4f6;
        }
        
        .grid-bg {
            background-size: 40px 40px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            position: absolute;
            inset: 0;
            z-index: -1;
            mask-image: radial-gradient(circle at center, black, transparent 80%);
        }

        .code-window {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        }
        
        .typewriter {
            overflow: hidden;
            border-right: .15em solid #0ea5e9;
            white-space: nowrap;
            margin: 0 auto;
            letter-spacing: .1em;
            animation: 
                typing 3.5s steps(40, end),
                blink-caret .75s step-end infinite;
        }
        
        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }
        
        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: #0ea5e9; }
        }
    </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center p-6 relative overflow-hidden">

    <!-- Subtle animated grid background -->
    <div class="grid-bg"></div>

    <!-- Glowing Orbs -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-cyan-600/20 rounded-full blur-[100px] animate-pulse-slow"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-600/20 rounded-full blur-[100px] animate-pulse-slow" style="animation-delay: 1.5s;"></div>

    <div class="w-full max-w-3xl z-10 animate-float">
        <!-- MacOS-like Window -->
        <div class="code-window bg-[#0d1117] border border-gray-800 rounded-xl overflow-hidden backdrop-blur-xl bg-opacity-90">
            
            <!-- Window Header -->
            <div class="flex items-center px-4 py-3 bg-[#161b22] border-b border-gray-800">
                <div class="flex space-x-2">
                    <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                </div>
                <div class="flex-1 text-center font-mono text-xs text-gray-500">
                    <svg class="inline w-4 h-4 mr-1 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    UAS_Project_WSA.json
                </div>
            </div>

            <!-- Terminal Content -->
            <div class="p-8 font-mono text-sm sm:text-base">
                
                <div class="mb-8">
                    <div class="inline-block">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2 typewriter">
                            <span class="text-cyan-400">~/ParfumStore-API</span> $ _
                        </h1>
                    </div>
                    <p class="text-gray-400 mt-2">RESTful API Architecture initialized successfully. Connection established.</p>
                </div>

                <!-- Code Block Data Diri -->
                <div class="bg-[#090c10] border border-gray-800 rounded-lg p-5 mb-8 shadow-inner overflow-x-auto">
                    <div class="flex text-gray-500 text-xs mb-3 select-none">
                        <span class="mr-4">1</span>
                        <span class="text-purple-400">const</span> <span class="text-blue-400">studentData</span> <span class="text-white">=</span> {
                    </div>
                    
                    <div class="flex text-gray-300">
                        <span class="text-gray-500 text-xs mr-4 select-none">2</span>
                        <span class="ml-6"><span class="text-cyan-200">name</span>: <span class="text-green-300">"Muhammad Rakha Syamputra"</span>,</span>
                    </div>
                    
                    <div class="flex text-gray-300 mt-2">
                        <span class="text-gray-500 text-xs mr-4 select-none">3</span>
                        <span class="ml-6"><span class="text-cyan-200">npm</span>: <span class="text-orange-300">"2310631250024"</span>,</span>
                    </div>
                    
                    <div class="flex text-gray-300 mt-2">
                        <span class="text-gray-500 text-xs mr-4 select-none">4</span>
                        <span class="ml-6"><span class="text-cyan-200">class</span>: <span class="text-green-300">"6A Sistem Informasi"</span>,</span>
                    </div>
                    
                    <div class="flex text-gray-300 mt-2">
                        <span class="text-gray-500 text-xs mr-4 select-none">5</span>
                        <span class="ml-6"><span class="text-cyan-200">status</span>: <span class="text-blue-300">true</span> <span class="text-gray-500 italic">// Ready for evaluation</span></span>
                    </div>
                    
                    <div class="flex text-gray-500 text-xs mt-3 select-none">
                        <span class="mr-4">6</span>
                        };
                    </div>
                </div>

                <!-- Metrics / Badges -->
                <div class="flex flex-wrap gap-3 mb-10">
                    <span class="px-3 py-1 rounded-full bg-cyan-900/30 text-cyan-400 border border-cyan-800/50 text-xs font-semibold flex items-center">
                        <span class="w-2 h-2 rounded-full bg-cyan-400 mr-2 animate-pulse"></span>
                        Laravel 12
                    </span>
                    <span class="px-3 py-1 rounded-full bg-purple-900/30 text-purple-400 border border-purple-800/50 text-xs font-semibold flex items-center">
                        <span class="w-2 h-2 rounded-full bg-purple-400 mr-2 animate-pulse"></span>
                        Sanctum Auth
                    </span>
                    <span class="px-3 py-1 rounded-full bg-orange-900/30 text-orange-400 border border-orange-800/50 text-xs font-semibold flex items-center">
                        <span class="w-2 h-2 rounded-full bg-orange-400 mr-2 animate-pulse"></span>
                        19 Endpoints
                    </span>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="/docs" class="flex-1 group relative inline-flex items-center justify-center font-sans px-8 py-3.5 font-bold text-white transition-all duration-200 bg-cyan-600 rounded-lg hover:bg-cyan-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-600 focus:ring-offset-gray-900 animate-glow">
                        <svg class="w-5 h-5 mr-2 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        API Documentation
                        <!-- Animated shine effect -->
                        <div class="absolute inset-0 h-full w-full overflow-hidden rounded-lg">
                            <div class="absolute top-0 left-[-100%] h-full w-1/2 -skew-x-12 bg-gradient-to-r from-transparent via-white/20 to-transparent group-hover:animate-[shine_1.5s_ease-in-out_infinite]"></div>
                        </div>
                    </a>
                    
                    <a href="/api/products" target="_blank" class="flex-1 inline-flex items-center justify-center font-sans px-8 py-3.5 font-semibold text-gray-300 transition-all duration-200 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-600 focus:ring-offset-gray-900">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"></path></svg>
                        Test /api/products
                    </a>
                </div>

            </div>
        </div>
        
        <div class="mt-6 text-center text-gray-500 text-xs font-mono">
            <p>System Online • Waiting for requests...</p>
        </div>
    </div>

    <style>
        @keyframes shine {
            100% { left: 200% }
        }
    </style>
</body>
</html>

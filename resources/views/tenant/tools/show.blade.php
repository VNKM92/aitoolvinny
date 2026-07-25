<x-tenant-layout :pages="$pages" :locale="$locale" :seo="$seo" :jsonLd="$jsonLd">
    <div class="max-w-4xl mx-auto space-y-6" x-data="{ activeView: 'interactive' }">
        <!-- Tool Header -->
        <div class="flex items-center justify-between pb-6 border-b border-slate-900">
            <div>
                <a href="{{ route('tenant.tools.index', ['locale' => $locale]) }}" class="text-[10px] font-bold text-indigo-400 hover:text-indigo-300 uppercase tracking-wider flex items-center mb-2">
                    &larr; Back to all tools
                </a>
                <h1 class="text-2xl font-bold text-white tracking-tight">
                    {{ $tool->translate('name', $locale) }}
                </h1>
                <p class="text-xs text-slate-400 mt-1">
                    {{ $tool->translate('description', $locale) }}
                </p>
            </div>
        </div>

        <!-- Tabbed Navigation -->
        <div class="flex space-x-1 border-b border-slate-900 pb-px">
            <button @click="activeView = 'interactive'" 
                :class="activeView === 'interactive' ? 'border-indigo-500 text-white font-bold' : 'border-transparent text-slate-400 hover:text-white'"
                class="px-4 py-2 text-xs font-bold border-b-2 transition-all duration-200">
                Interactive Utility
            </button>
            <button @click="activeView = 'api'" 
                :class="activeView === 'api' ? 'border-indigo-500 text-white font-bold' : 'border-transparent text-slate-400 hover:text-white'"
                class="px-4 py-2 text-xs font-bold border-b-2 transition-all duration-200">
                Developer API Docs
            </button>
        </div>

        <!-- 1. Interactive Tool Container -->
        <div x-show="activeView === 'interactive'" class="backdrop-blur-md bg-slate-900/40 border border-slate-900 p-8 rounded-2xl shadow-xl">
            
            <!-- QR CODE GENERATOR -->
            @if($tool->slug === 'qr-code-generator')
                <div x-data="{ qrData: 'https://google.com', qrSize: '200', qrUrl: '' }" x-init="qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=' + qrSize + 'x' + qrSize + '&data=' + encodeURIComponent(qrData)" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Text or URL to Encode</label>
                        <input x-model="qrData" @input="qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=' + qrSize + 'x' + qrSize + '&data=' + encodeURIComponent(qrData)" type="text" class="mt-2 block w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">QR Code Size (px)</label>
                            <select x-model="qrSize" @change="qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=' + qrSize + 'x' + qrSize + '&data=' + encodeURIComponent(qrData)" class="mt-2 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="150">150 x 150</option>
                                <option value="200">200 x 200</option>
                                <option value="250">250 x 250</option>
                                <option value="300">300 x 300</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-center p-6 bg-slate-950 border border-slate-900 rounded-xl space-y-4">
                        <img :src="qrUrl" alt="QR Code" class="bg-white p-3 rounded-lg border border-slate-800">
                        <a :href="qrUrl" download="qrcode.png" target="_blank" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-[10px] font-bold text-white uppercase tracking-wider transition-colors shadow-lg shadow-indigo-600/10">
                            Download QR Image
                        </a>
                    </div>
                </div>
            @endif

            <!-- PASSWORD GENERATOR -->
            @if($tool->slug === 'password-generator')
                <div x-data="{ length: 16, includeNumbers: true, includeSymbols: true, includeUpper: true, password: '', copied: false, generate() {
                    let chars = 'abcdefghijklmnopqrstuvwxyz';
                    if (this.includeNumbers) chars += '0123456789';
                    if (this.includeSymbols) chars += '!@#$%^&*()_+~`|}{[]:;?><,./-=';
                    if (this.includeUpper) chars += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    let pass = '';
                    for (let i = 0; i < this.length; i++) {
                        pass += chars.charAt(Math.floor(Math.random() * chars.length));
                    }
                    this.password = pass;
                    this.copied = false;
                } }" x-init="generate()" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Generated Password</label>
                        <div class="mt-2 flex space-x-2">
                            <input x-model="password" type="text" readonly class="block w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm font-mono text-indigo-400 focus:outline-none">
                            <button @click="navigator.clipboard.writeText(password); copied = true; setTimeout(() => copied = false, 2000)" class="px-4 py-3 bg-slate-850 border border-slate-800 text-white rounded-xl text-xs font-bold uppercase transition-all">
                                <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Password Length: <span class="text-white" x-text="length"></span></span>
                            <input x-model="length" @input="generate()" type="range" min="8" max="64" class="w-1/2 accent-indigo-500 bg-slate-950 h-2 rounded-lg">
                        </div>
                        <div class="flex flex-wrap gap-4 py-2">
                            <label class="flex items-center cursor-pointer select-none">
                                <input type="checkbox" x-model="includeNumbers" @change="generate()" class="sr-only peer">
                                <div class="w-5 h-5 bg-slate-950 border border-slate-800 rounded flex items-center justify-center peer-checked:bg-indigo-600 mr-2 transition-all">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <span class="text-xs text-slate-450">Numbers</span>
                            </label>
                            <label class="flex items-center cursor-pointer select-none">
                                <input type="checkbox" x-model="includeSymbols" @change="generate()" class="sr-only peer">
                                <div class="w-5 h-5 bg-slate-950 border border-slate-800 rounded flex items-center justify-center peer-checked:bg-indigo-600 mr-2 transition-all">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <span class="text-xs text-slate-455">Symbols</span>
                            </label>
                            <label class="flex items-center cursor-pointer select-none">
                                <input type="checkbox" x-model="includeUpper" @change="generate()" class="sr-only peer">
                                <div class="w-5 h-5 bg-slate-950 border border-slate-800 rounded flex items-center justify-center peer-checked:bg-indigo-600 mr-2 transition-all">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                </div>
                                <span class="text-xs text-slate-455">Uppercase letters</span>
                            </label>
                        </div>
                    </div>
                    <button @click="generate()" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-bold text-white uppercase tracking-wider transition-colors shadow-lg shadow-indigo-600/10">
                        Regenerate Secure Password
                    </button>
                </div>
            @endif

            <!-- UUID GENERATOR -->
            @if($tool->slug === 'uuid-generator')
                <div x-data="{ uuid: '', copied: false, generate() {
                    this.uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                        var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                        return v.toString(16);
                    });
                    this.copied = false;
                } }" x-init="generate()" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Generated UUID v4 Key</label>
                        <div class="mt-2 flex space-x-2">
                            <input x-model="uuid" type="text" readonly class="block w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm font-mono text-indigo-400 focus:outline-none">
                            <button @click="navigator.clipboard.writeText(uuid); copied = true; setTimeout(() => copied = false, 2000)" class="px-4 py-3 bg-slate-850 border border-slate-800 text-white rounded-xl text-xs font-bold uppercase transition-all">
                                <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                            </button>
                        </div>
                    </div>
                    <button @click="generate()" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-bold text-white uppercase tracking-wider transition-colors shadow-lg shadow-indigo-600/10">
                        Generate New UUID Key
                    </button>
                </div>
            @endif

            <!-- BASE64 ENCODER -->
            @if($tool->slug === 'base64-encoder')
                <div x-data="{ input: '', output: '', copied: false, encode() {
                    try {
                        this.output = btoa(unescape(encodeURIComponent(this.input)));
                    } catch (e) {
                        this.output = 'Encoding failed: invalid characters.';
                    }
                    this.copied = false;
                } }" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Input Plain Text</label>
                        <textarea x-model="input" @input="encode()" rows="4" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Type or paste your clear text here..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Base64 Encoded Output</label>
                        <textarea x-model="output" readonly rows="4" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs font-mono text-indigo-400 focus:outline-none" placeholder="Encoded output will appear here..."></textarea>
                    </div>
                    <button @click="navigator.clipboard.writeText(output); copied = true; setTimeout(() => copied = false, 2000)" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-bold text-white uppercase tracking-wider transition-colors shadow-lg shadow-indigo-600/10">
                        <span x-text="copied ? 'Copied Output to Clipboard!' : 'Copy Base64 Output'"></span>
                    </button>
                </div>
            @endif

            <!-- BASE64 DECODER -->
            @if($tool->slug === 'base64-decoder')
                <div x-data="{ input: '', output: '', error: false, copied: false, decode() {
                    try {
                        this.output = decodeURIComponent(escape(atob(this.input)));
                        this.error = false;
                    } catch (e) {
                        this.output = 'Decoding failed: invalid Base64 structure.';
                        this.error = true;
                    }
                    this.copied = false;
                } }" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Input Base64 Text</label>
                        <textarea x-model="input" @input="decode()" rows="4" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Paste your Base64 string here..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Decoded Clear Output</label>
                        <textarea x-model="output" readonly rows="4" :class="error ? 'text-red-400' : 'text-indigo-400'" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs font-mono focus:outline-none" placeholder="Decoded output will appear here..."></textarea>
                    </div>
                    <button :disabled="error || output === ''" @click="navigator.clipboard.writeText(output); copied = true; setTimeout(() => copied = false, 2000)" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-bold text-white uppercase tracking-wider transition-colors shadow-lg shadow-indigo-600/10 disabled:opacity-50">
                        <span x-text="copied ? 'Copied Output to Clipboard!' : 'Copy Decoded Output'"></span>
                    </button>
                </div>
            @endif

            <!-- JSON FORMATTER -->
            @if($tool->slug === 'json-formatter')
                <div x-data="{ input: '', output: '', error: false, copied: false, format() {
                    if (this.input.trim() === '') {
                        this.output = '';
                        this.error = false;
                        return;
                    }
                    try {
                        let parsed = JSON.parse(this.input);
                        this.output = JSON.stringify(parsed, null, 4);
                        this.error = false;
                    } catch (e) {
                        this.output = 'Invalid JSON: ' + e.message;
                        this.error = true;
                    }
                    this.copied = false;
                } }" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Raw JSON Code</label>
                        <textarea x-model="input" @input="format()" rows="6" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder='{"key":"value","array":[1,2,3]}'></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Beautified & Validated JSON</label>
                        <textarea x-model="output" readonly rows="6" :class="error ? 'text-red-400' : 'text-indigo-400'" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs font-mono focus:outline-none" placeholder="Formatted output will appear here..."></textarea>
                    </div>
                    <button :disabled="error || output === ''" @click="navigator.clipboard.writeText(output); copied = true; setTimeout(() => copied = false, 2000)" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-bold text-white uppercase tracking-wider transition-colors shadow-lg shadow-indigo-600/10 disabled:opacity-50">
                        <span x-text="copied ? 'Copied Output to Clipboard!' : 'Copy Formatted JSON'"></span>
                    </button>
                </div>
            @endif

            <!-- SQL FORMATTER -->
            @if($tool->slug === 'sql-formatter')
                <div x-data="{ input: '', output: '', copied: false, format() {
                    let sql = this.input;
                    let keywords = ['select', 'from', 'where', 'and', 'or', 'join', 'left', 'right', 'inner', 'outer', 'on', 'group by', 'order by', 'having', 'limit', 'update', 'delete', 'insert into', 'values', 'set', 'create table'];
                    keywords.forEach(word => {
                        let regex = new RegExp('\\b' + word + '\\b', 'gi');
                        sql = sql.replace(regex, word.toUpperCase());
                    });
                    this.output = sql.trim();
                    this.copied = false;
                } }" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Raw SQL Query</label>
                        <textarea x-model="input" @input="format()" rows="6" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="select * from users where id = 5;"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider font-mono">Beautified SQL Query</label>
                        <textarea x-model="output" readonly rows="6" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs text-indigo-400 font-mono focus:outline-none" placeholder="Formatted SQL output..."></textarea>
                    </div>
                    <button @click="navigator.clipboard.writeText(output); copied = true; setTimeout(() => copied = false, 2000)" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-bold text-white uppercase tracking-wider transition-colors shadow-lg shadow-indigo-600/10">
                        <span x-text="copied ? 'Copied Output to Clipboard!' : 'Copy Formatted SQL'"></span>
                    </button>
                </div>
            @endif

            <!-- HTML FORMATTER -->
            @if($tool->slug === 'html-formatter')
                <div x-data="{ input: '', output: '', copied: false, format() {
                    let html = this.input.trim();
                    let formatted = '';
                    let reg = /(<[^>]+>)/g;
                    let elements = html.split(reg);
                    let indent = 0;
                    elements.forEach(el => {
                        if (el.trim() === '') return;
                        if (el.match(/^<\/\w/)) {
                            indent = Math.max(0, indent - 1);
                        }
                        formatted += '    '.repeat(indent) + el.trim() + '\n';
                        if (el.match(/^<\w/) && !el.match(/\/>$/) && !el.match(/^<(area|base|br|col|embed|hr|img|input|link|meta|param|source|track|wbr)/)) {
                            indent++;
                        }
                    });
                    this.output = formatted.trim();
                    this.copied = false;
                } }" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Raw HTML Code</label>
                        <textarea x-model="input" @input="format()" rows="6" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="<div><p>Hello World</p></div>"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Beautified HTML</label>
                        <textarea x-model="output" readonly rows="6" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs text-indigo-400 font-mono focus:outline-none" placeholder="Formatted HTML output..."></textarea>
                    </div>
                    <button @click="navigator.clipboard.writeText(output); copied = true; setTimeout(() => copied = false, 2000)" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-bold text-white uppercase tracking-wider transition-colors shadow-lg shadow-indigo-600/10">
                        <span x-text="copied ? 'Copied Output to Clipboard!' : 'Copy Formatted HTML'"></span>
                    </button>
                </div>
            @endif

            <!-- CSS MINIFIER -->
            @if($tool->slug === 'css-minifier')
                <div x-data="{ input: '', output: '', copied: false, minify() {
                    let css = this.input;
                    css = css.replace(/\/\*[\s\S]*?\*\//g, '');
                    css = css.replace(/\s*([{\}:;,])\s*/g, '$1');
                    css = css.replace(/\s+/g, ' ');
                    this.output = css.trim();
                    this.copied = false;
                } }" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Raw CSS Stylesheet</label>
                        <textarea x-model="input" @input="minify()" rows="6" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="body { background-color: black; margin: 0; }"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider font-mono">Minified CSS Code</label>
                        <textarea x-model="output" readonly rows="6" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs text-indigo-400 font-mono focus:outline-none" placeholder="Minified CSS output..."></textarea>
                    </div>
                    <button @click="navigator.clipboard.writeText(output); copied = true; setTimeout(() => copied = false, 2000)" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-bold text-white uppercase tracking-wider transition-colors shadow-lg shadow-indigo-600/10">
                        <span x-text="copied ? 'Copied Output to Clipboard!' : 'Copy Minified CSS'"></span>
                    </button>
                </div>
            @endif

            <!-- JS BEAUTIFIER -->
            @if($tool->slug === 'js-beautifier')
                <div x-data="{ input: '', output: '', copied: false, beautify() {
                    let js = this.input.trim();
                    let formatted = '';
                    let indent = 0;
                    let tokens = js.split(/([{}()\[\];,])/);
                    tokens.forEach(tok => {
                        if (tok.trim() === '') return;
                        if (tok === '}' || tok === ']') {
                            indent = Math.max(0, indent - 1);
                        }
                        formatted += '    '.repeat(indent) + tok.trim() + '\n';
                        if (tok === '{' || tok === '[') {
                            indent++;
                        }
                    });
                    this.output = formatted.trim();
                    this.copied = false;
                } }" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Raw JS Code</label>
                        <textarea x-model="input" @input="beautify()" rows="6" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="function test() { console.log('hi'); }"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Beautified Javascript</label>
                        <textarea x-model="output" readonly rows="6" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs text-indigo-400 font-mono focus:outline-none" placeholder="Beautified JS output..."></textarea>
                    </div>
                    <button @click="navigator.clipboard.writeText(output); copied = true; setTimeout(() => copied = false, 2000)" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-bold text-white uppercase tracking-wider transition-colors shadow-lg shadow-indigo-600/10">
                        <span x-text="copied ? 'Copied Output to Clipboard!' : 'Copy Beautified JS'"></span>
                    </button>
                </div>
            @endif

            <!-- WORD COUNTER -->
            @if($tool->slug === 'word-counter')
                <div x-data="{ text: '', words: 0, chars: 0, readingTime: 0, updateStats() {
                    let cleanText = this.text.trim();
                    this.words = cleanText === '' ? 0 : cleanText.split(/\s+/).length;
                    this.chars = this.text.length;
                    this.readingTime = Math.ceil(this.words / 200);
                } }" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Input Text Content</label>
                        <textarea x-model="text" @input="updateStats()" rows="8" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Paste article content to calculate statistics..."></textarea>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-slate-950 border border-slate-900 p-4 rounded-xl text-center">
                            <span class="block text-xs text-slate-500 uppercase font-semibold">Total Words</span>
                            <span class="block text-lg font-black text-white mt-1" x-text="words"></span>
                        </div>
                        <div class="bg-slate-950 border border-slate-900 p-4 rounded-xl text-center">
                            <span class="block text-xs text-slate-500 uppercase font-semibold">Total Characters</span>
                            <span class="block text-lg font-black text-white mt-1" x-text="chars"></span>
                        </div>
                        <div class="bg-slate-950 border border-slate-900 p-4 rounded-xl text-center">
                            <span class="block text-xs text-slate-500 uppercase font-semibold">Reading Time</span>
                            <span class="block text-lg font-black text-white mt-1"><span x-text="readingTime"></span> min</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- CHARACTER COUNTER -->
            @if($tool->slug === 'character-counter')
                <div x-data="{ text: '', chars: 0, charsNoSpaces: 0, updateStats() {
                    this.chars = this.text.length;
                    this.charsNoSpaces = this.text.replace(/\s+/g, '').length;
                } }" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Input Text Content</label>
                        <textarea x-model="text" @input="updateStats()" rows="8" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Type text here to count characters..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-950 border border-slate-900 p-4 rounded-xl text-center">
                            <span class="block text-xs text-slate-500 uppercase font-semibold">Characters (With spaces)</span>
                            <span class="block text-lg font-black text-white mt-1" x-text="chars"></span>
                        </div>
                        <div class="bg-slate-950 border border-slate-900 p-4 rounded-xl text-center">
                            <span class="block text-xs text-slate-500 uppercase font-semibold">Characters (No spaces)</span>
                            <span class="block text-lg font-black text-white mt-1" x-text="charsNoSpaces"></span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- SLUG GENERATOR -->
            @if($tool->slug === 'slug-generator')
                <div x-data="{ title: '', slug: '', copied: false, generate() {
                    this.slug = this.title.toLowerCase().trim()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/[\s_-]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    this.copied = false;
                } }" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Input Title String</label>
                        <input x-model="title" @input="generate()" type="text" class="mt-2 block w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. 10 Best SEO Practices For Beginners!">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Generated URL Slug</label>
                        <div class="mt-2 flex space-x-2">
                            <input x-model="slug" type="text" readonly class="block w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-xs font-mono text-indigo-400 focus:outline-none">
                            <button @click="navigator.clipboard.writeText(slug); copied = true; setTimeout(() => copied = false, 2000)" class="px-4 py-3 bg-slate-850 border border-slate-800 text-white rounded-xl text-xs font-bold uppercase transition-all">
                                <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- LOREM IPSUM GENERATOR -->
            @if($tool->slug === 'lorem-ipsum')
                <div x-data="{ paragraphs: 3, output: '', copied: false, generate() {
                    let base = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
                    let paragraphsList = [];
                    for(let i=0; i<this.paragraphs; i++) {
                        paragraphsList.push(base);
                    }
                    this.output = paragraphsList.join('\n\n');
                    this.copied = false;
                } }" x-init="generate()" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Number of Paragraphs</label>
                        <select x-model="paragraphs" @change="generate()" class="mt-2 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="1">1 Paragraph</option>
                            <option value="3">3 Paragraphs</option>
                            <option value="5">5 Paragraphs</option>
                            <option value="8">8 Paragraphs</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Generated Placeholders</label>
                        <textarea x-model="output" readonly rows="8" class="mt-2 block w-full p-4 bg-slate-950 border border-slate-800 rounded-xl text-xs text-slate-300 leading-relaxed focus:outline-none"></textarea>
                    </div>
                    <button @click="navigator.clipboard.writeText(output); copied = true; setTimeout(() => copied = false, 2000)" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-bold text-white uppercase tracking-wider transition-colors shadow-lg shadow-indigo-600/10">
                        <span x-text="copied ? 'Copied Output to Clipboard!' : 'Copy Placeholders'"></span>
                    </button>
                </div>
            @endif

            <!-- RANDOM PASSWORD GENERATOR (5 list) -->
            @if($tool->slug === 'random-password')
                <div x-data="{ list: [], generate() {
                    let chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
                    let passwords = [];
                    for(let p=0; p<5; p++) {
                        let pass = '';
                        for (let i = 0; i < 12; i++) {
                            pass += chars.charAt(Math.floor(Math.random() * chars.length));
                        }
                        passwords.push(pass);
                    }
                    this.list = passwords;
                } }" x-init="generate()" class="space-y-6">
                    <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">5 Randomized secure keys</h3>
                    <div class="space-y-2">
                        <template x-for="(pass, index) in list" :key="index">
                            <div class="flex items-center justify-between p-3 bg-slate-950 border border-slate-850 rounded-xl">
                                <span class="font-mono text-xs text-indigo-400" x-text="pass"></span>
                                <button @click="navigator.clipboard.writeText(pass); alert('Copied: ' + pass)" class="text-[10px] font-bold text-slate-400 hover:text-white uppercase transition-colors">
                                    Copy
                                </button>
                            </div>
                        </template>
                    </div>
                    <button @click="generate()" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-bold text-white uppercase tracking-wider transition-colors shadow-lg shadow-indigo-600/10">
                        Regenerate List
                    </button>
                </div>
            @endif

            <!-- AGE CALCULATOR -->
            @if($tool->slug === 'age-calculator')
                <div x-data="{ birthdate: '', result: '', calculate() {
                    if (!this.birthdate) return;
                    let today = new Date();
                    let birth = new Date(this.birthdate);
                    let years = today.getFullYear() - birth.getFullYear();
                    let months = today.getMonth() - birth.getMonth();
                    let days = today.getDate() - birth.getDate();
                    if (days < 0) {
                        months--;
                        days += new Date(today.getFullYear(), today.getMonth(), 0).getDate();
                    }
                    if (months < 0) {
                        years--;
                        months += 12;
                    }
                    this.result = `Age is ${years} years, ${months} months, and ${days} days.`;
                } }" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Select Birthdate</label>
                        <input x-model="birthdate" @change="calculate()" type="date" class="mt-2 block w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div x-show="result" class="p-4 bg-slate-950 border border-slate-900 rounded-xl text-center">
                        <span class="block text-xs text-slate-500 uppercase font-semibold">Calculated Age</span>
                        <span class="block text-sm font-bold text-white mt-1" x-text="result"></span>
                    </div>
                </div>
            @endif

            <!-- EMI CALCULATOR -->
            @if($tool->slug === 'emi-calculator')
                <div x-data="{ 
                    principal: 500000, 
                    rate: 9.5, 
                    tenure: 5, 
                    tenureType: 'years', 
                    emi: 0, 
                    interest: 0, 
                    payable: 0, 
                    principalPercent: 50, 
                    interestPercent: 50,
                    schedule: [], 
                    page: 1, 
                    perPage: 12,
                    totalPages() {
                        return Math.ceil(this.schedule.length / this.perPage) || 1;
                    },
                    paginatedSchedule() {
                        let start = (this.page - 1) * this.perPage;
                        return this.schedule.slice(start, start + this.perPage);
                    },
                    calculate() {
                        let p = parseFloat(this.principal) || 0;
                        let rawRate = parseFloat(this.rate) || 0;
                        let n = parseInt(this.tenure) || 0;
                        if (this.tenureType === 'years') {
                            n = n * 12;
                        }
                        if (p > 0 && n > 0) {
                            if (rawRate <= 0) {
                                // 0% Interest case
                                let emiVal = p / n;
                                this.emi = Math.round(emiVal);
                                this.payable = p;
                                this.interest = 0;
                                this.principalPercent = 100;
                                this.interestPercent = 0;

                                let balance = p;
                                let sched = [];
                                for (let i = 1; i <= n; i++) {
                                    let principalMonth = emiVal;
                                    balance = balance - principalMonth;
                                    if (balance < 0) balance = 0;
                                    
                                    sched.push({
                                        month: i,
                                        beginningBalance: Math.round(balance + principalMonth),
                                        principalPaid: Math.round(principalMonth),
                                        interestPaid: 0,
                                        endingBalance: Math.round(balance)
                                    });
                                }
                                this.schedule = sched;
                            } else {
                                // Standard EMI compounding
                                let r = rawRate / 12 / 100;
                                let emiVal = (p * r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1);
                                this.emi = Math.round(emiVal);
                                this.payable = Math.round(emiVal * n);
                                this.interest = Math.round(this.payable - p);
                                
                                this.principalPercent = Math.round((p / this.payable) * 100);
                                this.interestPercent = 100 - this.principalPercent;

                                let balance = p;
                                let sched = [];
                                for (let i = 1; i <= n; i++) {
                                    let interestMonth = balance * r;
                                    let principalMonth = emiVal - interestMonth;
                                    balance = balance - principalMonth;
                                    if (balance < 0) balance = 0;
                                    
                                    sched.push({
                                        month: i,
                                        beginningBalance: Math.round(balance + principalMonth),
                                        principalPaid: Math.round(principalMonth),
                                        interestPaid: Math.round(interestMonth),
                                        endingBalance: Math.round(balance)
                                    });
                                }
                                this.schedule = sched;
                            }
                        } else {
                            this.emi = 0;
                            this.interest = 0;
                            this.payable = 0;
                            this.principalPercent = 0;
                            this.interestPercent = 0;
                            this.schedule = [];
                        }
                    }
                }" x-init="calculate()" class="space-y-8">
                    <!-- Controls Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Loan Amount -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <label class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Loan Amount (Principal)</label>
                                <div class="flex items-center space-x-1">
                                    <span class="text-xs text-indigo-400 font-bold">$</span>
                                    <input x-model.number="principal" @input="calculate(); page = 1" type="number" class="w-24 px-2 py-0.5 bg-slate-950 border border-slate-800 rounded text-right text-xs text-white font-bold focus:outline-none">
                                </div>
                            </div>
                            <input x-model.number="principal" @input="calculate(); page = 1" type="range" min="10000" max="10000000" step="10000" class="w-full accent-indigo-500 bg-slate-950 h-2 rounded-lg">
                            <div class="flex justify-between text-[8px] text-slate-500">
                                <span>$10k</span>
                                <span>$10M</span>
                            </div>
                        </div>

                        <!-- Interest Rate -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <label class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Interest Rate (% p.a.)</label>
                                <div class="flex items-center space-x-1">
                                    <input x-model.number="rate" @input="calculate(); page = 1" type="number" step="0.05" class="w-16 px-2 py-0.5 bg-slate-950 border border-slate-800 rounded text-right text-xs text-white font-bold focus:outline-none">
                                    <span class="text-xs text-indigo-400 font-bold">%</span>
                                </div>
                            </div>
                            <input x-model.number="rate" @input="calculate(); page = 1" type="range" min="0" max="30" step="0.1" class="w-full accent-indigo-500 bg-slate-950 h-2 rounded-lg">
                            <div class="flex justify-between text-[8px] text-slate-500">
                                <span>0%</span>
                                <span>30%</span>
                            </div>
                        </div>

                        <!-- Tenure -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <label class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Loan Tenure</label>
                                <div class="flex items-center space-x-2">
                                    <input x-model.number="tenure" @input="calculate(); page = 1" type="number" class="w-12 px-2 py-0.5 bg-slate-950 border border-slate-800 rounded text-right text-xs text-white font-bold focus:outline-none">
                                    <div class="flex rounded-md bg-slate-950 p-0.5 border border-slate-800">
                                        <button type="button" @click="tenureType = 'years'; calculate(); page = 1" :class="tenureType === 'years' ? 'bg-indigo-600 text-white' : 'text-slate-400'" class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase transition-all">Yr</button>
                                        <button type="button" @click="tenureType = 'months'; calculate(); page = 1" :class="tenureType === 'months' ? 'bg-indigo-600 text-white' : 'text-slate-400'" class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase transition-all">Mo</button>
                                    </div>
                                </div>
                            </div>
                            <input x-model.number="tenure" @input="calculate(); page = 1" type="range" :min="1" :max="tenureType === 'years' ? 30 : 360" step="1" class="w-full accent-indigo-500 bg-slate-950 h-2 rounded-lg">
                            <div class="flex justify-between text-[8px] text-slate-500">
                                <span>1</span>
                                <span x-text="tenureType === 'years' ? '30 Yrs' : '360 Mos'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Output Summary -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6 border-t border-slate-900">
                        <div class="bg-slate-950 border border-slate-900/60 p-4 rounded-xl text-center">
                            <span class="block text-xs text-slate-500 uppercase font-semibold">Monthly Loan EMI</span>
                            <span class="block text-xl font-black text-indigo-400 mt-1">$<span x-text="emi.toLocaleString()"></span></span>
                        </div>
                        <div class="bg-slate-950 border border-slate-900/60 p-4 rounded-xl text-center">
                            <span class="block text-xs text-slate-500 uppercase font-semibold">Total Interest Payable</span>
                            <span class="block text-xl font-black text-rose-450 mt-1">$<span x-text="interest.toLocaleString()"></span></span>
                        </div>
                        <div class="bg-slate-950 border border-slate-900/60 p-4 rounded-xl text-center">
                            <span class="block text-xs text-slate-500 uppercase font-semibold">Total Cost of Loan</span>
                            <span class="block text-xl font-black text-emerald-400 mt-1">$<span x-text="payable.toLocaleString()"></span></span>
                        </div>
                    </div>

                    <!-- Visual Breakdown Share Bar -->
                    <div class="space-y-2 pt-4">
                        <span class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Principal vs Interest Share Breakdown</span>
                        <div class="w-full h-4 rounded-full bg-slate-950 overflow-hidden flex">
                            <div :style="'width: ' + principalPercent + '%'" class="bg-indigo-600 h-full transition-all duration-300" title="Principal Portion"></div>
                            <div :style="'width: ' + interestPercent + '%'" class="bg-rose-600 h-full transition-all duration-300" title="Interest Portion"></div>
                        </div>
                        <div class="flex justify-between items-center text-[10px]">
                            <div class="flex items-center space-x-2">
                                <div class="w-2.5 h-2.5 bg-indigo-600 rounded"></div>
                                <span class="text-slate-400">Principal Amount: <span class="text-white font-bold" x-text="principalPercent"></span>%</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-2.5 h-2.5 bg-rose-600 rounded"></div>
                                <span class="text-slate-400">Interest Payable: <span class="text-white font-bold" x-text="interestPercent"></span>%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Amortization Schedule Table -->
                    <div x-show="schedule.length > 0" class="pt-6 border-t border-slate-900 space-y-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xs font-bold text-white uppercase tracking-wider">Loan Amortization Schedule (Monthly Breakup)</h3>
                            <span class="text-[10px] text-slate-500">Page <span x-text="page"></span> of <span x-text="totalPages()"></span></span>
                        </div>

                        <div class="overflow-x-auto border border-slate-850 rounded-xl">
                            <table class="w-full text-left border-collapse text-[10px]">
                                <thead>
                                    <tr class="bg-slate-950 text-slate-400 font-semibold uppercase border-b border-slate-850">
                                        <th class="px-4 py-2 text-center">Month</th>
                                        <th class="px-4 py-2">Beginning Balance</th>
                                        <th class="px-4 py-2">Principal Paid</th>
                                        <th class="px-4 py-2">Interest Paid</th>
                                        <th class="px-4 py-2">Ending Balance</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-900 text-slate-300">
                                    <template x-for="row in paginatedSchedule()" :key="row.month">
                                        <tr class="hover:bg-slate-900/10">
                                            <td class="px-4 py-2 text-center font-bold text-indigo-400" x-text="row.month"></td>
                                            <td class="px-4 py-2 font-mono" x-text="'$' + row.beginningBalance.toLocaleString()"></td>
                                            <td class="px-4 py-2 font-mono text-emerald-400" x-text="'$' + row.principalPaid.toLocaleString()"></td>
                                            <td class="px-4 py-2 font-mono text-rose-450" x-text="'$' + row.interestPaid.toLocaleString()"></td>
                                            <td class="px-4 py-2 font-mono text-slate-450" x-text="'$' + row.endingBalance.toLocaleString()"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Table Pagination Controls -->
                        <div class="flex justify-between items-center pt-2">
                            <button type="button" :disabled="page === 1" @click="page--" class="px-3 py-1 bg-slate-950 hover:bg-slate-900 border border-slate-850 text-[10px] font-bold text-slate-400 hover:text-white rounded-lg transition-colors disabled:opacity-30">
                                &larr; Previous Page
                            </button>
                            <button type="button" :disabled="page === totalPages()" @click="page++" class="px-3 py-1 bg-slate-950 hover:bg-slate-900 border border-slate-850 text-[10px] font-bold text-slate-400 hover:text-white rounded-lg transition-colors disabled:opacity-30">
                                Next Page &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- GST CALCULATOR -->
            @if($tool->slug === 'gst-calculator')
                <div x-data="{ amount: 1000, rate: 18, gstAmount: 0, totalAmount: 0, calculate() {
                    let amt = parseFloat(this.amount);
                    let r = parseFloat(this.rate);
                    if (amt > 0 && r >= 0) {
                        this.gstAmount = Math.round((amt * r) / 100);
                        this.totalAmount = Math.round(amt + this.gstAmount);
                    }
                } }" x-init="calculate()" class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">Base Net Price</label>
                            <input x-model="amount" @input="calculate()" type="number" class="mt-1.5 block w-full px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-slate-400">GST Rate (%)</label>
                            <select x-model="rate" @change="calculate()" class="mt-1.5 block w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="5">5% GST</option>
                                <option value="12">12% GST</option>
                                <option value="18">18% GST</option>
                                <option value="28">28% GST</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-900">
                        <div class="bg-slate-950 border border-slate-900 p-4 rounded-xl text-center">
                            <span class="block text-xs text-slate-500 uppercase font-semibold">Calculated GST Tax</span>
                            <span class="block text-lg font-black text-white mt-1" x-text="gstAmount"></span>
                        </div>
                        <div class="bg-slate-950 border border-slate-900 p-4 rounded-xl text-center">
                            <span class="block text-xs text-slate-500 uppercase font-semibold">Total Gross Price</span>
                            <span class="block text-lg font-black text-white mt-1" x-text="totalAmount"></span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- PERCENTAGE CALCULATOR -->
            @if($tool->slug === 'percentage-calculator')
                <div x-data="{ value1: 10, value2: 200, res1: 0, value3: 15, value4: 60, res2: 0, calc1() {
                    this.res1 = Math.round((parseFloat(this.value1) / 100) * parseFloat(this.value2));
                }, calc2() {
                    this.res2 = Math.round((parseFloat(this.value3) / parseFloat(this.value4)) * 100);
                } }" x-init="calc1(); calc2();" class="space-y-8">
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider">What is X% of Y?</h4>
                        <div class="flex flex-col sm:flex-row items-center gap-2">
                            <input x-model="value1" @input="calc1()" type="number" class="w-full sm:w-24 px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none">
                            <span class="text-xs text-slate-500">% of</span>
                            <input x-model="value2" @input="calc1()" type="number" class="w-full sm:w-24 px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none">
                            <span class="text-xs text-slate-500">=</span>
                            <span class="text-sm font-bold text-white" x-text="res1"></span>
                        </div>
                    </div>

                    <div class="space-y-4 pt-6 border-t border-slate-900">
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider">X is what % of Y?</h4>
                        <div class="flex flex-col sm:flex-row items-center gap-2">
                            <input x-model="value3" @input="calc2()" type="number" class="w-full sm:w-24 px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none">
                            <span class="text-xs text-slate-500">is what % of</span>
                            <input x-model="value4" @input="calc2()" type="number" class="w-full sm:w-24 px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs text-white focus:outline-none">
                            <span class="text-xs text-slate-500">=</span>
                            <span class="text-sm font-bold text-white"><span x-text="res2"></span>%</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- IMAGE COMPRESSOR -->
            @if($tool->slug === 'image-compressor')
                <div x-data="{ quality: 0.75, imageSrc: '', compressedSrc: '', origSize: 0, newSize: 0, loading: false,
                    handleUpload(e) {
                        const file = e.target.files[0];
                        if (!file) return;
                        this.origSize = Math.round(file.size / 1024);
                        const reader = new FileReader();
                        reader.onload = (event) => {
                            this.imageSrc = event.target.result;
                            this.compress();
                        };
                        reader.readAsDataURL(file);
                    },
                    compress() {
                        if (!this.imageSrc) return;
                        this.loading = true;
                        const img = new Image();
                        img.src = this.imageSrc;
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            canvas.width = img.width;
                            canvas.height = img.height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0);
                            const dataUrl = canvas.toDataURL('image/jpeg', parseFloat(this.quality));
                            this.compressedSrc = dataUrl;
                            const head = 'data:image/jpeg;base64,';
                            this.newSize = Math.round((dataUrl.length - head.length) * 3 / 4 / 1024);
                            this.loading = false;
                        };
                    }
                }" class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Select Image to Compress</label>
                        <input type="file" accept="image/jpeg,image/png" @change="handleUpload($event)" class="mt-2 block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:uppercase file:bg-indigo-600 file:text-white file:cursor-pointer hover:file:bg-indigo-500">
                    </div>

                    <div x-show="imageSrc" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Compression Quality: <span class="text-white" x-text="Math.round(quality*100)"></span>%</span>
                            <input x-model="quality" @input="compress()" type="range" min="0.1" max="1.0" step="0.05" class="w-1/2 accent-indigo-500 bg-slate-950 h-2 rounded-lg">
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-900">
                            <div class="bg-slate-950 border border-slate-900 p-4 rounded-xl text-center">
                                <span class="block text-xs text-slate-500 uppercase font-semibold">Original Size</span>
                                <span class="block text-sm font-bold text-white mt-1"><span x-text="origSize"></span> KB</span>
                            </div>
                            <div class="bg-slate-950 border border-slate-900 p-4 rounded-xl text-center">
                                <span class="block text-xs text-slate-500 uppercase font-semibold">Compressed Size</span>
                                <span class="block text-sm font-bold text-indigo-400 mt-1"><span x-text="newSize"></span> KB</span>
                            </div>
                        </div>

                        <div x-show="compressedSrc" class="flex justify-center pt-2">
                            <a :href="compressedSrc" download="compressed.jpg" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-bold text-white uppercase tracking-wider transition-colors shadow-lg shadow-indigo-600/10">
                                Download Compressed Image
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <!-- 2. Developer API Docs Tab -->
        <div x-show="activeView === 'api'" style="display: none;" class="backdrop-blur-md bg-slate-900/40 border border-slate-900 p-8 rounded-2xl shadow-xl space-y-6">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Developer REST API Reference</h3>
            <p class="text-xs text-slate-450 leading-relaxed">Consume this tool programmatically inside your applications. Requests are rate-limited to 60 executions per minute per IP address.</p>
            
            <div class="space-y-4">
                <div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-950 border border-indigo-900 text-indigo-400 uppercase mr-2">POST</span>
                    <code class="text-xs text-slate-300 font-mono select-all">
                        {{ url('/') }}/api/tools/{{ $tool->slug === 'image-compressor' ? 'image-compress' : ($tool->slug === 'css-minifier' ? 'css-minify' : ($tool->slug === 'js-beautifier' ? 'js-beautify' : ($tool->slug === 'uuid-generator' ? 'uuid' : ($tool->slug === 'password-generator' || $tool->slug === 'random-password' ? 'password' : ($tool->slug === 'qr-code-generator' ? 'qr-code' : $tool->slug))))) }}
                    </code>
                </div>

                <div class="border-t border-slate-900 pt-4">
                    <h4 class="text-xs font-bold text-white mb-2">Request Example (cURL)</h4>
                    @php
                        $apiUrl = url('/') . '/api/tools/' . ($tool->slug === 'image-compressor' ? 'image-compress' : ($tool->slug === 'css-minifier' ? 'css-minify' : ($tool->slug === 'js-beautifier' ? 'js-beautify' : ($tool->slug === 'uuid-generator' ? 'uuid' : ($tool->slug === 'password-generator' || $tool->slug === 'random-password' ? 'password' : ($tool->slug === 'qr-code-generator' ? 'qr-code' : $tool->slug))))));
                        
                        $payload = match($tool->slug) {
                            'qr-code-generator' => '{\n  "data": "https://google.com",\n  "size": 250\n}',
                            'password-generator', 'random-password' => '{\n  "length": 16,\n  "numbers": true,\n  "symbols": true\n}',
                            'base64-encoder' => '{\n  "text": "Hello World"\n}',
                            'base64-decoder' => '{\n  "text": "SGVsbG8gV29ybGQ="\n}',
                            'json-formatter' => '{\n  "text": "{\\"key\\":\\"value\\"}"\n}',
                            'sql-formatter' => '{\n  "text": "select * from users;"\n}',
                            'html-formatter' => '{\n  "text": "<div><p>test</p></div>"\n}',
                            'css-minifier' => '{\n  "text": "body { margin: 0; }"\n}',
                            'js-beautifier' => '{\n  "text": "function test(){}"\n}',
                            'word-counter', 'character-counter' => '{\n  "text": "Sample text to count."\n}',
                            'slug-generator' => '{\n  "text": "Amazing SEO Title!"\n}',
                            'lorem-ipsum' => '{\n  "paragraphs": 3\n}',
                            'age-calculator' => '{\n  "birthdate": "1995-05-15"\n}',
                            'emi-calculator' => '{\n  "principal": 100000,\n  "rate": 8.5,\n  "tenure": 12\n}',
                            'gst-calculator' => '{\n  "amount": 1000,\n  "rate": 18\n}',
                            'percentage-calculator' => '{\n  "type": "of",\n  "value1": 10,\n  "value2": 200\n}',
                            default => '{}'
                        };
                    @endphp
                    <div class="bg-slate-950 p-4 rounded-xl border border-slate-900 relative">
                        <pre class="text-[10px] text-slate-300 font-mono overflow-x-auto select-all">
@if($tool->slug === 'image-compressor')
curl -X POST {{ $apiUrl }} \
  -F "file=@image.jpg" \
  -F "quality=0.8"
@elseif($tool->slug === 'uuid-generator')
curl -X POST {{ $apiUrl }}
@else
curl -X POST {{ $apiUrl }} \
  -H "Content-Type: application/json" \
  -d '{!! str_replace('\n', "\n", $payload) !!}'
@endif</pre>
                    </div>
                </div>

                <div class="border-t border-slate-900 pt-4">
                    <h4 class="text-xs font-bold text-white mb-2">Expected JSON Response</h4>
                    @php
                        $responseBody = match($tool->slug) {
                            'qr-code-generator' => '{\n  "success": true,\n  "qr_code_url": "https://api.qrserver.com/..."\n}',
                            'password-generator', 'random-password' => '{\n  "success": true,\n  "password": "xY8$mP2#qL1"\n}',
                            'uuid-generator' => '{\n  "success": true,\n  "uuid": "4cd2540b-7822-4a0b-93ff-f368f5cd798b"\n}',
                            'base64-encoder' => '{\n  "success": true,\n  "encoded": "SGVsbG8gV29ybGQ="\n}',
                            'base64-decoder' => '{\n  "success": true,\n  "decoded": "Hello World"\n}',
                            'json-formatter' => '{\n  "success": true,\n  "formatted": "{\\n  \\"key\\": \\"value\\"\\n}"\n}',
                            'sql-formatter' => '{\n  "success": true,\n  "formatted": "SELECT * FROM users;"\n}',
                            'html-formatter' => '{\n  "success": true,\n  "formatted": "<div>\\n  <p>test</p>\\n</div>"\n}',
                            'css-minifier' => '{\n  "success": true,\n  "minified": "body{margin:0;}"\n}',
                            'js-beautifier' => '{\n  "success": true,\n  "formatted": "function test() {\\n}"\n}',
                            'word-counter', 'character-counter' => '{\n  "success": true,\n  "words": 5,\n  "characters": 21,\n  "reading_time_minutes": 1\n}',
                            'slug-generator' => '{\n  "success": true,\n  "slug": "amazing-seo-title"\n}',
                            'lorem-ipsum' => '{\n  "success": true,\n  "lorem": "Lorem ipsum dolor sit amet..."\n}',
                            'age-calculator' => '{\n  "success": true,\n  "years": 31,\n  "months": 2,\n  "days": 9,\n  "summary": "31 years, 2 months, and 9 days."\n}',
                            'emi-calculator' => '{\n  "success": true,\n  "monthly_emi": 8722.44,\n  "total_interest": 4669.25,\n  "total_payable": 104669.25\n}',
                            'gst-calculator' => '{\n  "success": true,\n  "base_amount": 1000,\n  "gst_tax": 180,\n  "gross_total": 1180\n}',
                            'percentage-calculator' => '{\n  "success": true,\n  "result": 20\n}',
                            'image-compressor' => '{\n  "success": true,\n  "original_size_kb": 245.12,\n  "compressed_size_kb": 85.40,\n  "base64_image": "data:image/jpeg;base64,..."\n}',
                            default => '{}'
                        };
                    @endphp
                    <div class="bg-slate-950 p-4 rounded-xl border border-slate-900">
                        <pre class="text-[10px] text-indigo-400 font-mono overflow-x-auto">{!! str_replace('\n', "\n", $responseBody) !!}</pre>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-tenant-layout>

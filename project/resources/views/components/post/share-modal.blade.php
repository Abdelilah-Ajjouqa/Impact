<div x-data="{
        open: false,
        url: '',
        title: '',
        copied: false,
        init() {
            window.addEventListener('open-share-modal', (e) => {
                this.url = e.detail.url || window.location.href;
                this.title = e.detail.title || document.title;
                this.copied = false;
                this.open = true;
            });
        },
        copyToClipboard() {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(this.url).then(() => {
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2500);
                });
            } else {
                const input = this.$refs.shareUrlInput;
                input.select();
                document.execCommand('copy');
                this.copied = true;
                setTimeout(() => this.copied = false, 2500);
            }
        },
        shareTwitter() {
            window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(this.url)}&text=${encodeURIComponent(this.title)}`, '_blank', 'width=600,height=400');
        },
        shareWhatsApp() {
            window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(this.title + ' ' + this.url)}`, '_blank');
        },
        sharePinterest() {
            window.open(`https://pinterest.com/pin/create/button/?url=${encodeURIComponent(this.url)}&description=${encodeURIComponent(this.title)}`, '_blank', 'width=600,height=500');
        },
        shareLinkedIn() {
            window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(this.url)}`, '_blank', 'width=600,height=500');
        },
        shareFacebook() {
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(this.url)}`, '_blank', 'width=600,height=400');
        }
    }"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity"
    @keydown.escape.window="open = false">

    <!-- Modal Card -->
    <div @click.away="open = false"
        x-show="open"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative border border-gray-100">

        <!-- Close Button -->
        <button @click="open = false"
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100 transition">
            <i class="fas fa-times text-lg"></i>
        </button>

        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-sky-50 text-sky-500 mb-3 shadow-inner">
                <i class="fas fa-share-nodes text-xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900">Share this post</h3>
            <p class="text-sm text-gray-500 mt-1 line-clamp-1" x-text="title"></p>
        </div>

        <!-- Social Share Icons Grid -->
        <div class="grid grid-cols-5 gap-3 mb-6">
            <!-- Twitter / X -->
            <button @click="shareTwitter"
                class="flex flex-col items-center p-2 rounded-xl hover:bg-gray-50 group transition">
                <div class="w-11 h-11 rounded-full bg-black text-white flex items-center justify-center group-hover:scale-110 transition shadow-sm">
                    <i class="fab fa-x-twitter text-lg"></i>
                </div>
                <span class="text-xs text-gray-600 mt-1.5">X</span>
            </button>

            <!-- WhatsApp -->
            <button @click="shareWhatsApp"
                class="flex flex-col items-center p-2 rounded-xl hover:bg-gray-50 group transition">
                <div class="w-11 h-11 rounded-full bg-emerald-500 text-white flex items-center justify-center group-hover:scale-110 transition shadow-sm">
                    <i class="fab fa-whatsapp text-xl"></i>
                </div>
                <span class="text-xs text-gray-600 mt-1.5">WhatsApp</span>
            </button>

            <!-- Pinterest -->
            <button @click="sharePinterest"
                class="flex flex-col items-center p-2 rounded-xl hover:bg-gray-50 group transition">
                <div class="w-11 h-11 rounded-full bg-red-600 text-white flex items-center justify-center group-hover:scale-110 transition shadow-sm">
                    <i class="fab fa-pinterest text-xl"></i>
                </div>
                <span class="text-xs text-gray-600 mt-1.5">Pinterest</span>
            </button>

            <!-- LinkedIn -->
            <button @click="shareLinkedIn"
                class="flex flex-col items-center p-2 rounded-xl hover:bg-gray-50 group transition">
                <div class="w-11 h-11 rounded-full bg-blue-700 text-white flex items-center justify-center group-hover:scale-110 transition shadow-sm">
                    <i class="fab fa-linkedin-in text-lg"></i>
                </div>
                <span class="text-xs text-gray-600 mt-1.5">LinkedIn</span>
            </button>

            <!-- Facebook -->
            <button @click="shareFacebook"
                class="flex flex-col items-center p-2 rounded-xl hover:bg-gray-50 group transition">
                <div class="w-11 h-11 rounded-full bg-blue-600 text-white flex items-center justify-center group-hover:scale-110 transition shadow-sm">
                    <i class="fab fa-facebook-f text-lg"></i>
                </div>
                <span class="text-xs text-gray-600 mt-1.5">Facebook</span>
            </button>
        </div>

        <!-- Copy Link Field -->
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Or copy direct link</label>
            <div class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-xl border border-gray-200 focus-within:border-sky-500 focus-within:ring-2 focus-within:ring-sky-100 transition">
                <input x-ref="shareUrlInput"
                    type="text"
                    readonly
                    :value="url"
                    class="bg-transparent text-sm text-gray-700 px-2 py-1 w-full focus:outline-none truncate font-mono">
                <button @click="copyToClipboard"
                    :class="copied ? 'bg-emerald-500 text-white' : 'bg-gray-900 hover:bg-black text-white'"
                    class="shrink-0 text-xs font-semibold py-2 px-3.5 rounded-lg transition-all flex items-center gap-1.5">
                    <i :class="copied ? 'fas fa-check' : 'fas fa-copy'"></i>
                    <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                </button>
            </div>
        </div>

        <!-- Copy Toast Message -->
        <div x-show="copied"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="mt-4 text-center text-xs font-medium text-emerald-600 flex items-center justify-center gap-1.5">
            <i class="fas fa-check-circle"></i> Link copied to clipboard!
        </div>
    </div>
</div>


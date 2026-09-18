<?php
$file = 'resources/views/layouts/study.blade.php';
$content = file_get_contents($file);

$oldProfile = <<<HTML
            {{-- Profile --}}
            <div class="p-4 border-t border-white/10">
                <div role="button" tabindex="0" onclick="openProfileModal()"
                    onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openProfileModal();}"
                    class="w-full flex items-center justify-between gap-3 rounded-xl hover:bg-white/10 transition p-2 -m-2 text-left focus:outline-none cursor-pointer">
                    <span class="flex items-center gap-3 min-w-0">
                        <span class="w-9 h-9 rounded-full bg-[#E7C8DB] text-[#5B1744] flex items-center justify-center font-bold text-xs shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}
                        </span>
                        <span class="min-w-0">
                            <span class="block font-semibold text-xs text-white truncate">
                                {{ auth()->user()->name ?? 'Killa' }}
                            </span>
                            <span class="block text-[10px] text-white/50 truncate">
                                {{ auth()->user()->email }}
                            </span>
                        </span>
                    </span>

                    <span class="flex items-center gap-1 shrink-0 text-white/50">
                        <i class="fa-solid fa-pen-to-square text-xs" title="Edit profile"></i>

                        <form method="POST" action="{{ route('logout') }}" onclick="event.stopPropagation()">
                            @csrf
                            <button type="submit" title="Logout"
                                class="text-white/50 hover:text-white transition p-1.5 rounded-lg hover:bg-white/10">
                                <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                            </button>
                        </form>
                    </span>
                </div>
            </div>
HTML;

$newProfile = <<<HTML
            {{-- Profile --}}
            <div class="p-4 border-t border-white/10 flex items-center justify-between gap-2">
                <div role="button" tabindex="0" onclick="openProfileModal()"
                    onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openProfileModal();}"
                    class="flex-1 flex items-center gap-3 rounded-xl hover:bg-white/10 transition p-2 -m-2 text-left focus:outline-none cursor-pointer min-w-0">
                    <span class="flex items-center gap-3 min-w-0">
                        <span class="w-9 h-9 rounded-full bg-[#E7C8DB] text-[#5B1744] flex items-center justify-center font-bold text-xs shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}
                        </span>
                        <span class="min-w-0">
                            <span class="block font-semibold text-xs text-white truncate">
                                {{ auth()->user()->name ?? 'User' }}
                            </span>
                            <span class="block text-[10px] text-white/50 truncate">
                                {{ auth()->user()->email }}
                            </span>
                        </span>
                    </span>
                </div>

                <div class="flex items-center shrink-0 ml-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Logout"
                            class="text-white/50 hover:text-white transition p-2.5 rounded-lg hover:bg-white/10 bg-black/10">
                            <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
HTML;

$content = str_replace($oldProfile, $newProfile, $content);
file_put_contents($file, $content);
echo "Patched layout profile.\n";

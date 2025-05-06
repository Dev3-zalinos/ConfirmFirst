@if (is_plugin_active('language'))
    @php
        $supportedLocales = Language::getSupportedLocales();
        $languageDisplay = setting('language_display', 'all');
    @endphp

    @if ($supportedLocales && count($supportedLocales) > 1)
        <div>
            @if (setting('language_switcher_display', 'dropdown') === 'dropdown')
                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        @if ($languageDisplay == 'all' || $languageDisplay == 'flag')
                            {!! language_flag(Language::getCurrentLocaleFlag(), Language::getCurrentLocaleName()) !!}
                        @endif
                        @if ($languageDisplay == 'all' || $languageDisplay == 'name')
                            {{ Language::getCurrentLocaleName() }}
                        @endif
                    </a>

                    <ul class="dropdown-menu" @if ($languageDisplay == 'flag') style="min-width: 57px;" @endif>
                        @foreach ($supportedLocales as $localeCode => $properties)
                            @if ($localeCode != Language::getCurrentLocale())
                                <li>
                                    <a class="dropdown-item" href="{{ Language::getSwitcherUrl($localeCode, $properties['lang_code']) }}">
                                        @if ($languageDisplay == 'all' || $languageDisplay == 'flag')
                                            {!! language_flag($properties['lang_flag'], $properties['lang_name']) !!}
                                        @endif
                                        @if ($languageDisplay == 'all' || $languageDisplay == 'name')
                                            &nbsp;<span>{{ $properties['lang_name'] }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @else
                @foreach ($supportedLocales as $localeCode => $properties)
                    @if ($localeCode != Language::getCurrentLocale())
                        <a href="{{ Language::getSwitcherUrl($localeCode, $properties['lang_code']) }}" class="text-white ms-2">
                            @if ($languageDisplay == 'all' || $languageDisplay == 'flag')
                                {!! language_flag($properties['lang_flag'], $properties['lang_name']) !!}
                            @endif
                            @if ($languageDisplay == 'all' || $languageDisplay == 'name')
                                {{ $properties['lang_name'] }}
                            @endif
                        </a>
                    @endif
                @endforeach
            @endif
        </div>
    @endif
@endif

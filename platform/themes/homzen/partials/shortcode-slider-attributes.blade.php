data-autoplay="{{ $shortcode->is_autoplay === 'no' ? 'false' : 'true' }}"
data-autoplay-speed="{{ in_array($shortcode->autoplay_speed, [2000, 3000, 4000, 5000, 6000, 7000, 8000, 9000, 10000]) ? $shortcode->autoplay_speed : 5000 }}"
data-loop="{{ $shortcode->is_loop === 'no' ? 'false' : 'true' }}"

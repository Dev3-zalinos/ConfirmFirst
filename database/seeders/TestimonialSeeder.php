<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Testimonial\Models\Testimonial;

class TestimonialSeeder extends BaseSeeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'Jennifer Lee',
                'company' => 'Happy Home Seeker',
                'content' => 'From the initial consultation to closing day, the real estate team went above and beyond to ensure I found the perfect home. Their dedication and professionalism made the entire process seamless. Thank you!',
            ],
            [
                'name' => 'Robert Evans',
                'company' => 'Property Investor',
                'content' => 'I am impressed by the level of expertise and commitment demonstrated by this real estate team. Their insights into the market helped me make informed investment decisions, and I couldn\'t be happier with the results.',
            ],
            [
                'name' => 'Jessica White',
                'company' => 'Delighted Home Seller',
                'content' => 'Selling my home with the help of this real estate team was a breeze. They provided valuable advice, staged my property beautifully, and negotiated a great deal. I highly recommend their services to anyone looking to sell their home!',
            ],
            [
                'name' => 'Daniel Miller',
                'company' => 'Happy New Homeowner',
                'content' => 'Thanks to the expertise and guidance of this real estate team, I am now the proud owner of my dream home. They listened to my preferences, answered all my questions, and made the entire home buying process a positive experience.',
            ],
        ];

        Testimonial::query()->truncate();

        $files = $this->getFilesFromPath('avatars');

        foreach ($testimonials as $item) {
            Testimonial::query()->create([
                ...$item,
                'image' => $files->unique()->random(),
            ]);
        }
    }
}

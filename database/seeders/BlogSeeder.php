<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Blog\Database\Traits\HasBlogSeeder;
use Illuminate\Support\Facades\File;

class BlogSeeder extends BaseSeeder
{
    use HasBlogSeeder;

    public function run(): void
    {
        $this->uploadFiles('posts');

        $this->createBlogCategories(array_map(fn ($category) => ['name' => $category], [
            'Buying a Home',
            'Selling a Home',
            'Market Trends',
            'Home Improvement',
            'Real Estate Investing',
            'Neighborhood Guides',
        ]));

        $this->createBlogTags(array_map(fn ($tag) => ['name' => $tag], [
            'Tips',
            'Investing',
            'Market Analysis',
            'DIY',
            'Luxury Homes',
            'First-time Buyers',
            'Property Management',
            'Renovation',
        ]));

        $content = File::get(database_path('seeders/contents/post.html'));

        $this->createBlogPosts(array_map(function ($post) use ($content) {
            return [
                'name' => $post,
                'content' => $content,
                'image' => $this->filePath(sprintf('posts/%s.jpg', $this->faker->numberBetween(1, 10))),
            ];
        }, [
            'Top 10 Tips for First-time Home Buyers',
            'How to Stage Your Home for a Quick Sale',
            'Understanding the Current Real Estate Market Trends',
            'DIY Home Improvement Projects That Add Value',
            'A Beginner’s Guide to Real Estate Investing',
            'How to Choose the Right Neighborhood for Your Family',
            'Luxury Homes: What to Look For',
            'Property Management: Best Practices for Landlords',
            'Renovation Ideas to Increase Your Home’s Value',
            'The Ultimate Guide to Buying a Vacation Home',
            'How to Successfully Sell Your Home in a Buyer’s Market',
            'Home Inspection: What to Expect and How to Prepare',
            'Eco-friendly Home Improvements for Sustainable Living',
            'How to Navigate the Mortgage Process',
            'Real Estate Market Analysis: What You Need to Know',
            'Tips for Renting Out Your Property',
            'Understanding Property Taxes and How to Lower Them',
            'The Benefits of Smart Home Technology',
        ]));
    }
}

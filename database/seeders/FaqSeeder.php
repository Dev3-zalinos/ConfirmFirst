<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Faq\Models\Faq;
use Botble\Faq\Models\FaqCategory;

class FaqSeeder extends BaseSeeder
{
    public function run(): void
    {
        Faq::query()->truncate();
        FaqCategory::query()->truncate();

        $categories = [
            [
                'name' => 'Buying',
            ],
            [
                'name' => 'Selling',
            ],
            [
                'name' => 'Renting',
            ],
            [
                'name' => 'Financing',
            ],
        ];

        foreach ($categories as $index => $category) {
            $category['order'] = $index;
            $faqCategory = FaqCategory::query()->create($category);
            $this->seedFaqItems($faqCategory);
        }
    }

    protected function seedFaqItems($category): void
    {
        $faqItems = [
            [
                'question' => 'What steps are involved in buying a home?',
                'answer' => 'The home buying process involves several steps including getting pre-approved for a mortgage, finding a real estate agent, searching for homes, making an offer, getting a home inspection, and closing the deal.',
            ],
            [
                'question' => 'How do I determine my budget for buying a home?',
                'answer' => 'To determine your budget, consider your income, debts, and savings. It is also important to get pre-approved for a mortgage to understand how much you can borrow.',
            ],
            [
                'question' => 'What is the process of selling a home?',
                'answer' => 'Selling a home involves preparing your home for sale, setting a competitive price, marketing the property, showing the home to potential buyers, negotiating offers, and closing the sale.',
            ],
            [
                'question' => 'How can I increase the value of my home before selling?',
                'answer' => 'You can increase your home\'s value by making necessary repairs, updating outdated features, improving curb appeal, and ensuring the home is clean and well-maintained.',
            ],
            [
                'question' => 'What should I look for in a rental property?',
                'answer' => 'When looking for a rental property, consider factors such as location, rent price, amenities, lease terms, and the condition of the property. It\'s also important to understand your rights as a tenant.',
            ],
            [
                'question' => 'What are the benefits of renting versus buying?',
                'answer' => 'Renting offers flexibility and fewer maintenance responsibilities, while buying can provide long-term financial benefits and the freedom to customize your home. The decision depends on your financial situation, lifestyle, and future plans.',
            ],
            [
                'question' => 'What types of financing options are available for homebuyers?',
                'answer' => 'Common financing options include fixed-rate mortgages, adjustable-rate mortgages, FHA loans, VA loans, and USDA loans. Each has its own requirements and benefits.',
            ],
            [
                'question' => 'How do I improve my credit score for a mortgage?',
                'answer' => 'To improve your credit score, pay your bills on time, reduce your debt, avoid opening new credit accounts, and check your credit report for errors.',
            ],
        ];

        $randomItems = array_rand($faqItems, rand(5, 7));

        foreach ($randomItems as $index) {
            Faq::query()->create([
                'question' => $faqItems[$index]['question'],
                'answer' => $faqItems[$index]['answer'],
                'category_id' => $category->id,
            ]);
        }
    }
}

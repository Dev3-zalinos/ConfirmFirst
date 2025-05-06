@php
    // Get all published categories
    $categories = get_property_categories([
        'indent' => '↳',
        'conditions' => ['status' => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED],
    ]);
    
    // Define the category mappings with their IDs
    $residentialCategories = [
        'Apartment' => 1,
        'Villa' => 2,
        'Townhouse' => 8,
        'Penthouse' => 9,
        'Villa Compound' => 10,
        'Hotel Apartment' => 11,
        'Land' => 5,
        'Floor' => 12,
        'Building' => 13
    ];
    
    $commercialCategories = [
        'Office' => 14,
        'Shop' => 15,
        'Warehouse' => 16,
        'Labour Camp' => 17,
        'Villa' => 18,
        'Bulk Unit' => 19,
        'Land' => 20,
        'Floor' => 21,
        'Building' => 22,
        'Factory' => 23,
        'Industrial Land' => 24,
        'Mixed Use Land' => 25,
        'Showroom' => 26,
        'Other Commercial' => 27
    ];
    
    // Get selected category
    $selectedCategory = request()->query('category_id');
    $selectedCategoryName = '';
    
    // Find the name of the selected category
    foreach ($categories as $category) {
        if ($selectedCategory == $category->getKey()) {
            $selectedCategoryName = $category->name;
            break;
        }
    }
@endphp

<div class="form-group-3 form-style form-search-category" @if (theme_option('real_estate_enable_advanced_search', 'yes') != 'yes') style="border: none" @endif>
    <label for="category_id">{{ __('Category') }}</label>
    <div class="group-select">
        <!-- Keep the original select (but hidden) for form submission compatibility -->
        <select name="category_id" id="category_id" class="select_js d-none">
            <option value="">{{ __('All') }}</option>
            @foreach($categories as $category)
                <option value="{{ $category->getKey() }}"@selected(request()->query('category_id') == $category->getKey())>{{ $category->name }}</option>
            @endforeach
        </select>
        
        <!-- Custom dropdown implementation -->
        <div class="custom-category-dropdown">
            <button type="button" class="form-control dropdown-toggle-custom" id="categoryDropdownBtn">
                <span class="selected-category">{{ $selectedCategoryName ?: __('All') }}</span>
            </button>
            <div class="dropdown-menu-custom w-100 p-0" id="categoryDropdownMenu" style="display: none;">
                <!-- Tabs navigation -->
                <ul class="nav nav-tabs" id="categoryTabs" role="tablist">
                    <li class="nav-item" role="presentation" style="width: 50%">
                        <button class="nav-link active w-100" id="residential-tab" data-bs-toggle="tab" data-bs-target="#residential-tab-pane" type="button" role="tab">Residential</button>
                    </li>
                    <li class="nav-item" role="presentation" style="width: 50%">
                        <button class="nav-link w-100" id="commercial-tab" data-bs-toggle="tab" data-bs-target="#commercial-tab-pane" type="button" role="tab">Commercial</button>
                    </li>
                </ul>
                
                <!-- Tab content -->
                <div class="tab-content p-3" id="categoryTabContent">
                    <!-- Residential Tab -->
                    <div class="tab-pane fade show active" id="residential-tab-pane" role="tabpanel" aria-labelledby="residential-tab">
                        <div class="category-grid">
                            @foreach($residentialCategories as $name => $id)
                                <button type="button" class="category-option btn" data-category-id="{{ $id }}" data-category-name="{{ $name }}">{{ $name }}</button>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Commercial Tab -->
                    <div class="tab-pane fade" id="commercial-tab-pane" role="tabpanel" aria-labelledby="commercial-tab">
                        <div class="category-grid">
                            @foreach($commercialCategories as $name => $id)
                                <button type="button" class="category-option btn" data-category-id="{{ $id }}" data-category-name="{{ $name }}">{{ $name }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Action buttons -->
                <div class="d-flex justify-content-between p-2 border-top">
                    <button type="button" class="btn btn-outline-secondary" id="resetCategory">Reset</button>
                    <button type="button" class="btn btn-primary" id="doneCategory">Done</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Grid layout for category options */
.category-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    width: 100%;
    padding-bottom: 10px;
}

/* Custom styles for category option buttons */
.category-option {
    border: 1px solid #333;
    border-radius: 30px;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 13px;
    background-color: white;
    color: #333;
    transition: all 0.2s;
    width: 100%;
    text-align: center;
    min-height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1.2;
    word-break: keep-all;
    hyphens: none;
}

/* Specific fix for long category names */
.category-option[data-category-name="Hotel Apartment"],
.category-option[data-category-name="Villa Compound"],
.category-option[data-category-name="Labour Camp"],
.category-option[data-category-name="Industrial Land"],
.category-option[data-category-name="Mixed Use Land"],
.category-option[data-category-name="Other Commercial"] {
    font-size: 12px;
    padding: 6px 8px;
}

.category-option.selected {
    background-color: #000;
    color: white;
}

/* Tab styling */
#categoryTabs .nav-link {
    border: none;
    border-bottom: 2px solid transparent;
    border-radius: 0;
    color: #333;
    text-align: center;
    padding: 10px;
}

#categoryTabs .nav-link.active {
    border-bottom: 2px solid #000;
    color: #000;
    font-weight: 500;
}

/* Action buttons */
#resetCategory {
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    color: #666;
    transition: all 0.2s;
}

#resetCategory:hover {
    color: #000;
    border-color: #ccc;
    background-color: #f0f0f0;
}

#doneCategory {
    background-color: #000;
    border: 1px solid #000;
    color: white;
}

/* Custom dropdown styles */
.custom-category-dropdown {
    position: relative;
}

.dropdown-menu-custom {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    display: none;
    min-width: 10rem;
    padding: 0.5rem 0;
    margin: 0.125rem 0 0;
    font-size: 1rem;
    color: #212529;
    text-align: left;
    list-style: none;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0,0,0,.15);
    border-radius: 0.25rem;
}

.dropdown-toggle-custom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    text-align: left;
}

.dropdown-toggle-custom::after {
    display: inline-block;
    margin-left: 0.255em;
    vertical-align: 0.255em;
    content: "";
    border-top: 0.3em solid;
    border-right: 0.3em solid transparent;
    border-bottom: 0;
    border-left: 0.3em solid transparent;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const categoryInput = document.getElementById('category_id');
    const selectedCategoryText = document.querySelector('.selected-category');
    const categoryOptions = document.querySelectorAll('.category-option');
    const resetButton = document.getElementById('resetCategory');
    const doneButton = document.getElementById('doneCategory');
    const dropdownToggle = document.getElementById('categoryDropdownBtn');
    const dropdownMenu = document.getElementById('categoryDropdownMenu');
    const residentialTab = document.getElementById('residential-tab');
    const commercialTab = document.getElementById('commercial-tab');
    
    // Hide niceSelect if initialized
    const niceSelectElement = document.querySelector('.nice-select');
    if (niceSelectElement) {
        niceSelectElement.style.display = 'none';
    }
    
    // Initialize selected category
    if (categoryInput.value) {
        categoryOptions.forEach(option => {
            if (option.dataset.categoryId === categoryInput.value) {
                option.classList.add('selected');
            }
        });
    }
    
    // Toggle dropdown
    dropdownToggle.addEventListener('click', function() {
        dropdownMenu.style.display = dropdownMenu.style.display === 'none' ? 'block' : 'none';
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-category-dropdown')) {
            dropdownMenu.style.display = 'none';
        }
    });
    
    // Category option click handler
    categoryOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selection from all options
            categoryOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Add selected class
            this.classList.add('selected');
            
            // Update the text and hidden select
            selectedCategoryText.textContent = this.dataset.categoryName;
            categoryInput.value = this.dataset.categoryId;
            
            // Trigger change event on hidden select for any listeners
            const event = new Event('change');
            categoryInput.dispatchEvent(event);
        });
    });
    
    // Tab click handlers
    residentialTab.addEventListener('click', function() {
        residentialTab.classList.add('active');
        commercialTab.classList.remove('active');
        document.getElementById('residential-tab-pane').classList.add('show', 'active');
        document.getElementById('commercial-tab-pane').classList.remove('show', 'active');
    });
    
    commercialTab.addEventListener('click', function() {
        commercialTab.classList.add('active');
        residentialTab.classList.remove('active');
        document.getElementById('commercial-tab-pane').classList.add('show', 'active');
        document.getElementById('residential-tab-pane').classList.remove('show', 'active');
    });
    
    // Reset button handler
    resetButton.addEventListener('click', function() {
        categoryOptions.forEach(opt => opt.classList.remove('selected'));
        selectedCategoryText.textContent = 'All';
        categoryInput.value = '';
        
        // Trigger change event on hidden select
        const event = new Event('change');
        categoryInput.dispatchEvent(event);
    });
    
    // Done button handler
    doneButton.addEventListener('click', function() {
        dropdownMenu.style.display = 'none';
    });
});
</script>
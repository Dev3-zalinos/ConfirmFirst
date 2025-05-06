@php
    Theme::set('breadcrumbEnabled', 'no');
@endphp

<div style="margin-top: 120px; margin-bottom: 120px">
    <h4 class="text-danger">You need to setup your homepage first!</h4>

    <ul class="list-unstyled">
        <li class="mb-2"><strong>1. Go to Admin -> Plugins then activate all plugins.</strong></li>

        <li class="mb-2">
            <strong>2. Go to Admin -> Pages and create a page:</strong>

            <div class="mt-2">
                <label>Copy and paste the following code into the page content:</label>
                <div class="border p-2 mb-1">
                    <code>
                        <div>[hero-banner style="1" title="Find Your" animation_text="Dream Home,Perfect Home,Real
                            Estate" description="We are a real estate agency that will help you find the best residence
                            you dream of, let’s discuss for your dream house?" background_image="pages/slider-1.jpg"
                            search_box_enabled="1" projects_search_enabled="1"
                            default_search_type="project"][/hero-banner]
                        </div>
                        <div>[properties style="2" title="Recommended For You" subtitle="Features Properties"
                            category_ids="1,2,3,4,5,6" type="rent" is_featured="" limit="6" button_label="View All
                            Properties" button_url="/properties" enable_lazy_loading="yes"][/properties]
                        </div>
                        <div>[location title="Our Location For You" subtitle="Explore Cities" type="city"
                            city_ids="1,2,3,4,5" destination="property" background_color="#f7f7f7"
                            enable_lazy_loading="yes"][/location]
                        </div>
                        <div>[services style="1" title="What We Do?" subtitle="Our Services"
                            background_color="transparent" services_quantity="3" services_title_1="Buy A New Home"
                            services_description_1="Discover your dream home effortlessly. Explore diverse properties
                            and expert guidance for a seamless buying experience." services_button_label_1="Learn More"
                            services_button_url_1="/" services_icon_image_1="pages/service-1.png" services_title_2="Rent
                            A Home" services_description_2="Discover your perfect rental effortlessly. Explore a diverse
                            variety of listings tailored precisely to suit your unique lifestyle needs."
                            services_button_label_2="Learn More" services_button_url_2="/"
                            services_icon_image_2="pages/service-2.png" services_title_3="Sell A Home"
                            services_description_3="Sell confidently with expert guidance and effective strategies,
                            showcasing your property's best features for a successful sale."
                            services_button_label_3="Learn More" services_button_url_3="/"
                            services_icon_image_3="pages/service-3.png" counters_quantity="4"
                            counters_label_1="SATISFIED CLIENTS" counters_number_1="85" counters_label_2="AWARDS
                            RECEIVED" counters_number_2="112" counters_label_3="SUCCESSFUL TRANSACTIONS"
                            counters_number_3="32" counters_label_4="MONTHLY TRAFFIC" counters_number_4="66"
                            button_label="View All Services" button_url="/" enable_lazy_loading="yes"][/services]
                        </div>
                        <div>[services style="1" title="Why Choose Homzen" subtitle="Our Benefit"
                            background_color="#f7f7f7" services_quantity="3" services_title_1="Proven Expertise"
                            services_description_1="Our seasoned team excels in real estate with years of successful
                            market navigation, offering informed decisions and optimal results."
                            services_icon_image_1="pages/service-1.png" services_title_2="Customized Solutions"
                            services_description_2="We pride ourselves on crafting personalized strategies to match your
                            unique goals, ensuring a seamless real estate journey."
                            services_icon_image_2="pages/service-2.png" services_title_3="Transparent Partnerships"
                            services_description_3="Transparency is key in our client relationships. We prioritize clear
                            communication and ethical practices, fostering trust and reliability throughout."
                            services_icon_image_3="pages/service-3.png" counters_quantity="1" centered_content="1"
                            enable_lazy_loading="yes"][/services]
                        </div>
                        <div>[properties style="1" title="Best Property Value" subtitle="Top Properties"
                            is_featured="1" limit="4" button_label="View All" button_url="/properties"
                            enable_lazy_loading="yes"][/properties]
                        </div>
                        <div>[testimonials style="1" title="What’s People Say’s" subtitle="Top Properties"
                            description="Our seasoned team excels in real estate with years of successful market
                            navigation, offering informed decisions and optimal results." testimonial_ids="1,2,3,4"
                            background_color="#f7f7f7" enable_lazy_loading="yes"][/testimonials]
                        </div>
                        <div>[agents style="1" title="Meet Our Agents" subtitle="Our Teams" account_ids="1,2,3,4"
                            enable_lazy_loading="yes"][/agents]
                        </div>
                        <div>[blog-posts style="1" title="Helpful Homzen Guides" subtitle="Latest News"
                            type="recent" limit="3" background_color="#f7f7f7" enable_lazy_loading="yes"][/blog-posts]
                        </div>
                        <div>[image-slider background_color="transparent" quantity="7" name_1="GitHub"
                            image_1="partners/github.png" url_1="https://github.com" open_in_new_tab_1=""
                            name_2="LH.Tech" image_2="partners/lhtech.png" url_2="https://lhtech.com.my"
                            open_in_new_tab_2="" name_3="Panadoxn" image_3="partners/panadoxn.png" url_3="/"
                            open_in_new_tab_3="" name_4="Shangxi" image_4="partners/shangxi.png" url_4="/"
                            open_in_new_tab_4="" name_5="Tyaalpha" image_5="partners/tyaalpha.png" url_5="/"
                            open_in_new_tab_5="" name_6="Vanfaba" image_6="partners/vanfaba.png" url_6="/"
                            open_in_new_tab_6="" name_7="Asana" image_7="partners/asana.png" url_7="https://asana.com"
                            open_in_new_tab_7="" enable_lazy_loading="yes"][/image-slider]
                        </div>
                    </code>
                </div>

                <p>And choose <strong>Full width</strong> template.</p>
            </div>
        </li>

        <li><strong>3. Then go to Admin -> Appearance -> Theme options -> Page to set your homepage.</strong></li>
    </ul>
</div>

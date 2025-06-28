@extends('layouts.home_page.master')

@section('content')
<!-- Google Fonts for modern typography -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary-color: {{ $settings['theme_primary_color'] ?? '#56cc99' }};
        --secondary-color: {{ $settings['theme_secondary_color'] ?? '#215679' }};
        --secondary-color1: {{ $settings['theme_secondary_color_1'] ?? '#38a3a5' }};
        --primary-background-color: {{ $settings['theme_primary_background_color'] ?? '#f2f5f7' }};
        --text--secondary-color: {{ $settings['theme_text_secondary_color'] ?? '#5c788c' }};
        --modern-shadow: 0 4px 24px rgba(44, 62, 80, 0.08);
        --modern-radius: 18px;
        --modern-transition: 0.2s cubic-bezier(.4,0,.2,1);
        --section-spacing: 4rem;
    }
    body, html {
        font-family: 'Inter', Arial, sans-serif;
        background: var(--primary-background-color);
        color: #222;
    }
    .navbar {
        position: sticky;
        top: 0;
        z-index: 100;
        background: #fff;
        box-shadow: var(--modern-shadow);
        padding: 0.5rem 0;
        transition: box-shadow var(--modern-transition);
    }
    .navbarWrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
    }
    .navLogo img.logo {
        height: 48px;
        width: auto;
        border-radius: 8px;
    }
    .menuListWrapper ul.listItems {
        display: flex;
        gap: 1.5rem;
        align-items: center;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .menuListWrapper ul.listItems li a {
        font-weight: 600;
        color: var(--secondary-color);
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: background var(--modern-transition), color var(--modern-transition);
    }
    .menuListWrapper ul.listItems li a:hover, .menuListWrapper ul.listItems li a:focus {
        background: var(--primary-color);
        color: #fff;
    }
    .loginBtnsWrapper .commonBtn {
        border-radius: var(--modern-radius);
        box-shadow: var(--modern-shadow);
        font-weight: 600;
        padding: 0.5rem 1.5rem;
        margin-left: 0.5rem;
        background: var(--primary-color);
        color: #fff;
        border: none;
        transition: background var(--modern-transition), box-shadow var(--modern-transition);
    }
    .loginBtnsWrapper .commonBtn:hover {
        background: var(--secondary-color1);
        box-shadow: 0 8px 32px rgba(44, 62, 80, 0.12);
    }
    .hamburg {
        display: none;
        font-size: 2rem;
        cursor: pointer;
    }
    @media (max-width: 991px) {
        .menuListWrapper ul.listItems {
            display: none;
        }
        .hamburg {
            display: block;
        }
    }
    .main {
        margin-top: 2rem;
    }
    /* --- HERO SECTION --- */
    .heroSection {
        background: linear-gradient(120deg, var(--primary-color) 0%, var(--secondary-color1) 100%);
        color: #fff;
        border-radius: var(--modern-radius);
        box-shadow: var(--modern-shadow);
        padding: 4rem 0 3rem 0;
        margin-bottom: var(--section-spacing);
        position: relative;
        overflow: hidden;
        min-height: 480px;
        display: flex;
        align-items: center;
    }
    .heroSection .container {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
    }
    .heroSection .hero-content {
        flex: 1 1 400px;
        max-width: 540px;
        z-index: 2;
    }
    .heroSection .commonTitle {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 1.2rem;
        letter-spacing: -1px;
        line-height: 1.1;
    }
    .heroSection .commonDesc {
        font-size: 1.35rem;
        font-weight: 500;
        margin-bottom: 0.7rem;
    }
    .heroSection .commonText {
        font-size: 1.15rem;
        margin-bottom: 2.2rem;
        opacity: 0.97;
    }
    .heroSection .cta-group {
        display: flex;
        gap: 1.2rem;
        margin-bottom: 2rem;
    }
    .heroSection .commonBtn {
        border-radius: var(--modern-radius);
        font-size: 1.1rem;
        font-weight: 600;
        padding: 0.85rem 2.2rem;
        background: #fff;
        color: var(--primary-color);
        border: none;
        box-shadow: var(--modern-shadow);
        transition: background var(--modern-transition), color var(--modern-transition);
    }
    .heroSection .commonBtn:hover {
        background: var(--secondary-color1);
        color: #fff;
    }
    .heroSection .heroImgWrapper {
        flex: 1 1 320px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }
    .heroSection .heroImg img {
        max-width: 340px;
        border-radius: var(--modern-radius);
        box-shadow: var(--modern-shadow);
    }
    /* --- FEATURE GRID --- */
    .features-section {
        padding: var(--section-spacing) 0;
        background: #fff;
        border-radius: var(--modern-radius);
        box-shadow: var(--modern-shadow);
        margin-bottom: var(--section-spacing);
    }
    .features-section .sectionTitle {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 2rem;
    }
    .feature-card {
        background: #f9fafb;
        border-radius: var(--modern-radius);
        box-shadow: var(--modern-shadow);
        padding: 2rem 1.5rem;
        text-align: center;
        transition: box-shadow var(--modern-transition), transform var(--modern-transition);
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 220px;
    }
    .feature-card:hover {
        box-shadow: 0 8px 32px rgba(44, 62, 80, 0.14);
        transform: translateY(-4px) scale(1.03);
    }
    .feature-card img {
        width: 56px;
        height: 56px;
        margin-bottom: 1rem;
    }
    .feature-card .feature-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--secondary-color);
        margin-bottom: 0.5rem;
    }
    .feature-card .feature-desc {
        font-size: 1rem;
        color: var(--text--secondary-color);
        opacity: 0.9;
    }
    /* --- HOW IT WORKS --- */
    .how-it-works-section {
        padding: var(--section-spacing) 0;
        background: #fff;
        border-radius: var(--modern-radius);
        box-shadow: var(--modern-shadow);
        margin-bottom: var(--section-spacing);
    }
    .how-steps {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        justify-content: center;
        align-items: flex-start;
    }
    .how-step {
        flex: 1 1 220px;
        min-width: 220px;
        max-width: 300px;
        background: #f9fafb;
        border-radius: var(--modern-radius);
        box-shadow: var(--modern-shadow);
        padding: 2rem 1.2rem;
        text-align: center;
        margin-bottom: 1rem;
    }
    .how-step .step-icon {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }
    .how-step .step-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .how-step .step-desc {
        font-size: 1rem;
        color: var(--text--secondary-color);
    }
    /* --- TESTIMONIALS --- */
    .testimonials-section {
        padding: var(--section-spacing) 0;
        background: #fff;
        border-radius: var(--modern-radius);
        box-shadow: var(--modern-shadow);
        margin-bottom: var(--section-spacing);
    }
    .testimonials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
    }
    .testimonial-card {
        background: #f9fafb;
        border-radius: var(--modern-radius);
        box-shadow: var(--modern-shadow);
        padding: 2rem 1.5rem;
        text-align: left;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
    .testimonial-card .testimonial-quote {
        font-size: 1.1rem;
        font-style: italic;
        margin-bottom: 1rem;
    }
    .testimonial-card .testimonial-user {
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }
    .testimonial-card .testimonial-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        background: #e0e0e0;
    }
    .testimonial-card .testimonial-name {
        font-weight: 700;
        font-size: 1rem;
    }
    .testimonial-card .testimonial-role {
        font-size: 0.95rem;
        color: var(--text--secondary-color);
    }
    /* --- PRICING --- */
    .pricing-section {
        padding: var(--section-spacing) 0;
        background: #fff;
        border-radius: var(--modern-radius);
        box-shadow: var(--modern-shadow);
        margin-bottom: var(--section-spacing);
    }
    .pricing-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        justify-content: center;
    }
    .pricing-card {
        flex: 1 1 280px;
        max-width: 340px;
        background: #f9fafb;
        border-radius: var(--modern-radius);
        box-shadow: var(--modern-shadow);
        padding: 2.5rem 1.5rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        transition: box-shadow var(--modern-transition), transform var(--modern-transition);
    }
    .pricing-card.popular {
        border: 2px solid var(--primary-color);
        background: #fff;
        z-index: 2;
        box-shadow: 0 8px 32px rgba(44, 62, 80, 0.14);
        transform: scale(1.04);
    }
    .pricing-card .pricing-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .pricing-card .pricing-price {
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }
    .pricing-card .pricing-features {
        text-align: left;
        margin-bottom: 1.5rem;
    }
    .pricing-card .pricing-features span {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 1rem;
    }
    .pricing-card .pricingBtn {
        border-radius: var(--modern-radius);
        font-weight: 600;
        padding: 0.75rem 2rem;
        background: var(--primary-color);
        color: #fff;
        border: none;
        box-shadow: var(--modern-shadow);
        transition: background var(--modern-transition), color var(--modern-transition);
    }
    .pricing-card .pricingBtn:hover {
        background: var(--secondary-color1);
        color: #fff;
    }
    /* --- FAQ --- */
    .faq-section {
        padding: var(--section-spacing) 0;
        background: #fff;
        border-radius: var(--modern-radius);
        box-shadow: var(--modern-shadow);
        margin-bottom: var(--section-spacing);
    }
    .faq-section .accordion-item {
        border-radius: var(--modern-radius) !important;
        box-shadow: var(--modern-shadow);
        margin-bottom: 1rem;
        border: none;
    }
    .faq-section .accordion-button {
        font-weight: 600;
        font-size: 1.1rem;
        border-radius: var(--modern-radius) !important;
    }
    /* --- CONTACT --- */
    .contact-section {
        padding: var(--section-spacing) 0;
        background: #fff;
        border-radius: var(--modern-radius);
        box-shadow: var(--modern-shadow);
        margin-bottom: var(--section-spacing);
    }
    .contact-section .card {
        background: #fff;
        border-radius: var(--modern-radius);
        box-shadow: var(--modern-shadow);
        padding: 2rem 1.5rem;
    }
    .contact-section input, .contact-section textarea {
        border-radius: var(--modern-radius);
        border: 1px solid #e0e0e0;
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        width: 100%;
        font-size: 1rem;
        font-family: 'Inter', Arial, sans-serif;
    }
    .contact-section input:focus, .contact-section textarea:focus {
        border-color: var(--primary-color);
        outline: none;
    }
    .infoWrapper {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .infoWrapper .icon {
        font-size: 1.5rem;
        color: var(--primary-color);
    }
    /* --- FOOTER --- */
    .footer {
        background: #fff;
        color: var(--secondary-color);
        padding: 2rem 0 1rem 0;
        text-align: center;
        border-top: 1px solid #e0e0e0;
        font-size: 1rem;
    }
    .footer .footer-links {
        margin-bottom: 1rem;
    }
    .footer .footer-links a {
        color: var(--secondary-color);
        margin: 0 1rem;
        text-decoration: none;
        font-weight: 600;
        transition: color var(--modern-transition);
    }
    .footer .footer-links a:hover {
        color: var(--primary-color);
    }
    .footer .footer-social {
        margin-bottom: 1rem;
    }
    .footer .footer-social a {
        color: var(--secondary-color);
        margin: 0 0.5rem;
        font-size: 1.3rem;
        transition: color var(--modern-transition);
    }
    .footer .footer-social a:hover {
        color: var(--primary-color);
    }
    /* Responsive tweaks */
    @media (max-width: 767px) {
        .navbarWrapper, .main, .heroSection, .features-section, .how-it-works-section, .testimonials-section, .pricing-section, .faq-section, .contact-section, .footer {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
        .heroSection {
            flex-direction: column;
            padding: 2rem 0 1rem 0;
        }
        .heroSection .container {
            flex-direction: column;
        }
        .sectionTitle {
            font-size: 1.5rem;
        }
        .how-steps, .pricing-grid {
            flex-direction: column;
        }
    }
</style>
<script src="{{ asset('assets/home_page/js/jquery-1-12-4.min.js') }}"></script>

<!-- ================= HERO SECTION ================= -->
<section class="heroSection" id="home">
    <div class="container">
        <div class="hero-content">
            <span class="commonTitle">{{ $settings['system_name']  ?? 'eSchool SaaS' }}</span>
            <span class="commonDesc">{{ $settings['tag_line'] }}</span>
            <span class="commonText">{{ $settings['hero_description'] }}</span>
            <div class="cta-group">
                <button class="commonBtn" data-bs-toggle="modal" data-bs-target="#staticBackdrop">{{ __('register_your_school') }}</button>
                @if ($isDemoSchool == 1)
                    <a href="{{ $demoSchoolUrl ?? url('/') }}" target="_blank" class="commonBtn">{{ __('demo_school') }}</a>
                @endif
            </div>
        </div>
        <div class="heroImgWrapper">
            <img src="{{ $settings['home_image'] ?? asset('assets/landing_page_images/heroImg.png') }}" alt="">
        </div>
    </div>
    @include('registration_form')
</section>

<!-- ================= FEATURES SECTION ================= -->
<section class="features-section" id="features">
    <div class="container">
        <div class="sectionTitle">{{ __('explore_our_top_features') }}</div>
        <div class="features-grid">
            @foreach ($features as $key => $feature)
                <div class="feature-card">
                    <img src="{{ asset('assets/landing_page_images/features/') }}/{{ $feature->name }}.svg" alt="">
                    <div class="feature-title">{{ __($feature->name) }}</div>
                    <div class="feature-desc">{{ $feature->description ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ================= HOW IT WORKS SECTION ================= -->
<section class="how-it-works-section">
    <div class="container">
        <div class="sectionTitle">{{ __('how_it_works') }}</div>
        <div class="how-steps">
            <div class="how-step">
                <div class="step-icon"><i class="fa-solid fa-user-plus"></i></div>
                <div class="step-title">{{ __('register_school') }}</div>
                <div class="step-desc">{{ __('register_school_desc') ?? 'Sign up and set up your school profile in minutes.' }}</div>
            </div>
            <div class="how-step">
                <div class="step-icon"><i class="fa-solid fa-users"></i></div>
                <div class="step-title">{{ __('add_students_staff') }}</div>
                <div class="step-desc">{{ __('add_students_staff_desc') ?? 'Easily add students, staff, and classes.' }}</div>
            </div>
            <div class="how-step">
                <div class="step-icon"><i class="fa-solid fa-chart-line"></i></div>
                <div class="step-title">{{ __('track_progress') }}</div>
                <div class="step-desc">{{ __('track_progress_desc') ?? 'Monitor attendance, grades, and more from your dashboard.' }}</div>
            </div>
            <div class="how-step">
                <div class="step-icon"><i class="fa-solid fa-seedling"></i></div>
                <div class="step-title">{{ __('grow') }}</div>
                <div class="step-desc">{{ __('grow_desc') ?? 'Empower your school community and grow with insights.' }}</div>
            </div>
        </div>
    </div>
</section>

<!-- ================= TESTIMONIALS SECTION ================= -->
<section class="testimonials-section">
    <div class="container">
        <div class="sectionTitle">{{ __('what_our_users_say') }}</div>
        <div class="testimonials-grid">
            @foreach ($testimonials ?? [] as $testimonial)
                <div class="testimonial-card">
                    <div class="testimonial-quote">“{{ $testimonial->quote }}”</div>
                    <div class="testimonial-user">
                        <img src="{{ $testimonial->avatar ?? asset('assets/landing_page_images/user.png') }}" class="testimonial-avatar" alt="">
                        <div>
                            <div class="testimonial-name">{{ $testimonial->name }}</div>
                            <div class="testimonial-role">{{ $testimonial->role }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
            @if (empty($testimonials) || count($testimonials) == 0)
                <!-- Example testimonials if none exist -->
                <div class="testimonial-card">
                    <div class="testimonial-quote">“This platform made managing our school so much easier!”</div>
                    <div class="testimonial-user">
                        <img src="{{ asset('assets/landing_page_images/user.png') }}" class="testimonial-avatar" alt="">
                        <div>
                            <div class="testimonial-name">Jane Doe</div>
                            <div class="testimonial-role">Principal</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-quote">“I love the intuitive dashboard and quick support.”</div>
                    <div class="testimonial-user">
                        <img src="{{ asset('assets/landing_page_images/user.png') }}" class="testimonial-avatar" alt="">
                        <div>
                            <div class="testimonial-name">John Smith</div>
                            <div class="testimonial-role">Teacher</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- ================= PRICING SECTION ================= -->
<section class="pricing-section" id="pricing">
    <div class="container">
        <div class="sectionTitle">{{ __('flexible_pricing_packages') }}</div>
        <div class="pricing-grid">
            @foreach ($packages as $package)
                <div class="pricing-card @if($package->highlight) popular @endif">
                    <div class="pricing-title">{{ __($package->name) }}</div>
                    <div class="pricing-price">
                        @if ($package->is_trial == 1)
                            {{ __('free') }}
                        @elseif($package->type == 0 && $package->is_trial == 0)
                            {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($package->charges, 2) }}
                        @elseif($package->type == 1 && $package->is_trial == 0)
                            {{ $settings['currency_symbol'] ?? '$' }}{{ number_format($package->student_charge, 2) }} <span style="font-size:1rem;">/{{ __('per_student') }}</span>
                        @endif
                    </div>
                    <div class="pricing-features">
                        @foreach ($features as $feature)
                            <span @if (!in_array($feature->id, $package->package_feature->pluck('feature_id')->toArray())) style="text-decoration:line-through;opacity:0.5;" @endif>
                                {{ __($feature->name) }}
                            </span>
                        @endforeach
                    </div>
                    <button class="pricingBtn" data-bs-toggle="modal" data-bs-target="#staticBackdrop">{{ __('get_started') }}</button>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ================= FAQ SECTION ================= -->
@if (count($faqs))
<section class="faq-section" id="faq">
    <div class="container">
        <div class="sectionTitle">{{ __('frequently_asked_questions') }}</div>
        <div class="accordion" id="accordionExample">
            @foreach ($faqs as $faq)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne-{{ $faq->id }}" aria-expanded="true" aria-controls="collapseOne-{{ $faq->id }}">
                            <span>{{ $faq->title }}</span>
                        </button>
                    </h2>
                    <div id="collapseOne-{{ $faq->id }}" class="accordion-collapse collapse"
                        aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <span>{!! nl2br(e($faq->description)) !!}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ================= CONTACT SECTION ================= -->
<section class="contact-section" id="contact-us">
    <div class="container">
        <div class="sectionTitle">{{ __('lets_get_in_touch') }}</div>
        <div class="row">
            <div class="col-lg-6">
                <form action="{{ url('contact') }}" method="post" role="form" class="php-email-form mb-5 create-form-with-captcha">
                    @csrf
                    <div class="card">
                        <div>
                            <input type="text" required name="name" id="name" placeholder="{{ __('enter_your_name') }}">
                        </div>
                        <div>
                            <input type="email" required name="email" id="email" placeholder="{{ __('enter_your_email') }}">
                        </div>
                        <div>
                            <textarea name="message" required id="message" cols="30" rows="6"
                                placeholder="{{ __('send_your_message') }}"></textarea>
                        </div>
                        @if (config('services.recaptcha.key') ?? '')
                            <div>
                                <div class="g-recaptcha" data-sitekey={{config('services.recaptcha.key')}}></div>
                            </div>    
                        @endif
                        <div>
                            <button class="commonBtn">{{ __('send') }}</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-6 infoBox">
                <div class="infoWrapper">
                    <div>
                        <span class="icon"><i class="fa-solid fa-phone-volume"></i></span>
                    </div>
                    <div>
                        <span>{{ __('phone') }}</span>
                        <span>{{ __('mobile') }} : {{ $settings['mobile'] ?? '' }}</span>
                    </div>
                </div>
                <div class="infoWrapper">
                    <div>
                        <span class="icon"><i class="fa-solid fa-envelope-open-text"></i></span>
                    </div>
                    <div>
                        <span>{{ __('email') }}</span>
                        <span>{{ $settings['mail_send_from'] ?? 'example@gmail.com' }}</span>
                    </div>
                </div>
                <div class="infoWrapper">
                    <div>
                        <span class="icon"><i class="fa-solid fa-location-dot"></i></span>
                    </div>
                    <div>
                        <span>{{ __('location') }}</span>
                        <span>{{ $settings['address'] ?? '' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= FOOTER ================= -->
<footer class="footer">
    <div class="footer-links">
        <a href="#home">{{ __('home') }}</a>
        <a href="#features">{{ __('features') }}</a>
        <a href="#pricing">{{ __('pricing') }}</a>
        <a href="#faq">{{ __('faqs') }}</a>
        <a href="#contact-us">{{ __('contact') }}</a>
    </div>
    <div class="footer-social">
        <a href="#"><i class="fa-brands fa-facebook"></i></a>
        <a href="#"><i class="fa-brands fa-twitter"></i></a>
        <a href="#"><i class="fa-brands fa-linkedin"></i></a>
    </div>
    <div>&copy; {{ date('Y') }} {{ $settings['system_name']  ?? 'eSchool SaaS' }}. All rights reserved.</div>
</footer>

@endsection

@section('script')
<script async src="https://www.google.com/recaptcha/api.js"></script>
<script>
    $('.redirect-login').click(function (e) { 
        e.preventDefault();
        window.location.href = "{{ url('login') }}"
    });
</script>
<script>
    @if (Session::has('success'))
    $.toast({
        text: '{{ Session::get('success') }}',
        showHideTransition: 'slide',
        icon: 'success',
        loaderBg: '#f96868',
        position: 'top-right',
        bgColor: '#20CFB5'
    });
    @endif
    @if (Session::has('error'))
    $.toast({
        text: '{{ Session::get('error') }}',
        showHideTransition: 'slide',
        icon: 'error',
        loaderBg: '#f2a654',
        position: 'top-right',
        bgColor: '#FE7C96'
    });
    @endif
</script>
@endsection
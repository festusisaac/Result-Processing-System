@extends('layouts.public')

@section('content')
<div style="background-color: var(--primary); padding: 4rem 2rem; text-align: center; color: white;">
    <h1 style="font-size: 2.5rem; font-weight: 700;">Contact Us</h1>
    <p style="opacity: 0.9; margin-top: 1rem;">We'd love to hear from you. Get in touch with us.</p>
</div>

<div style="max-width: 1000px; margin: 4rem auto; padding: 0 2rem;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 4rem;">
        <!-- Contact Info -->
        <div>
            <h2 style="font-size: 1.75rem; color: var(--secondary); margin-bottom: 2rem;">Get in Touch</h2>
            
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.1rem; color: var(--primary); margin-bottom: 0.5rem;">Address</h3>
                <p style="color: var(--text-light);">123 Education Lane, Knowledge City,<br>State, Country 12345</p>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.1rem; color: var(--primary); margin-bottom: 0.5rem;">Phone</h3>
                <p style="color: var(--text-light);">+1 234 567 8900<br>+1 234 567 8901</p>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.1rem; color: var(--primary); margin-bottom: 0.5rem;">Email</h3>
                <p style="color: var(--text-light);">info@rmsschool.com<br>admissions@rmsschool.com</p>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.1rem; color: var(--primary); margin-bottom: 0.5rem;">Office Hours</h3>
                <p style="color: var(--text-light);">Monday - Friday: 8:00 AM - 4:00 PM<br>Saturday: 9:00 AM - 1:00 PM</p>
            </div>
        </div>
        
        <!-- Contact Form -->
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <h2 style="font-size: 1.75rem; color: var(--secondary); margin-bottom: 2rem;">Send Message</h2>
            
            @if(session('success'))
                <div style="background: #10b981; color: white; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div style="background: #ef4444; color: white; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                    {{ session('error') }}
                </div>
            @endif
            
            @if($errors->any())
                <div style="background: #ef4444; color: white; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('public.contact.send') }}" method="POST">
                @csrf
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Your Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; outline: none;" placeholder="John Doe">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; outline: none;" placeholder="john@example.com">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Subject</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; outline: none;" placeholder="Inquiry about admissions">
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Message</label>
                    <textarea name="message" rows="5" required style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; outline: none;" placeholder="How can we help you?">{{ old('message') }}</textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message</button>
            </form>
        </div>
    </div>
</div>
@endsection

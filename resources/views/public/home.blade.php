@extends('layouts.public')

@section('content')
<!-- Hero Section -->
<section style="background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%); color: white; padding: 8rem 2rem; position: relative; overflow: hidden;">
    <!-- Decorative Circle -->
    <div style="position: absolute; top: -100px; right: -100px; width: 400px; height: 400px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
    <div style="position: absolute; bottom: -50px; left: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
    
    <div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
        <div style="z-index: 1;">
            <span style="display: inline-block; background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 2rem; font-size: 0.875rem; font-weight: 600; margin-bottom: 1.5rem;">Welcome to Excellence</span>
            <h1 style="font-size: 4rem; font-weight: 800; margin-bottom: 1.5rem; line-height: 1.1;">Empowering the <br><span style="color: #fbbf24;">Next Generation</span></h1>
            <p style="font-size: 1.25rem; margin-bottom: 2.5rem; opacity: 0.9; max-width: 500px; line-height: 1.8;">We provide a world-class education that nurtures creativity, critical thinking, and character development in every student.</p>
            <div style="display: flex; gap: 1rem;">
                <a href="{{ route('public.contact') }}" class="btn" style="background-color: white; color: var(--primary); padding: 1rem 2rem; font-size: 1.1rem; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);">Get Started</a>
                <a href="{{ route('public.contact') }}" class="btn" style="border: 2px solid rgba(255,255,255,0.5); color: white; padding: 1rem 2rem; font-size: 1.1rem;">Contact Us</a>
            </div>
        </div>
        <div style="display: none; @media(min-width: 768px){ display: block; }">
            <!-- Placeholder for Hero Image -->
            <div style="background: rgba(255,255,255,0.1); border-radius: 2rem; padding: 2rem; backdrop-filter: blur(10px); transform: rotate(3deg);">
                <div style="background: #cbd5e1; height: 400px; border-radius: 1.5rem; display: flex; align-items: center; justify-content: center; color: #64748b; font-weight: 600;">
                    School Building / Students Image
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section style="background: white; padding: 4rem 2rem; margin-top: -4rem; position: relative; z-index: 10;">
    <div style="max-width: 1000px; margin: 0 auto; background: white; border-radius: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); padding: 3rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; text-align: center;">
        <div>
            <div style="font-size: 3rem; font-weight: 800; color: var(--primary); margin-bottom: 0.5rem;">25+</div>
            <div style="color: var(--text-light); font-weight: 500;">Years of Excellence</div>
        </div>
        <div>
            <div style="font-size: 3rem; font-weight: 800; color: var(--primary); margin-bottom: 0.5rem;">1500+</div>
            <div style="color: var(--text-light); font-weight: 500;">Happy Students</div>
        </div>
        <div>
            <div style="font-size: 3rem; font-weight: 800; color: var(--primary); margin-bottom: 0.5rem;">100+</div>
            <div style="color: var(--text-light); font-weight: 500;">Expert Teachers</div>
        </div>
        <div>
            <div style="font-size: 3rem; font-weight: 800; color: var(--primary); margin-bottom: 0.5rem;">100%</div>
            <div style="color: var(--text-light); font-weight: 500;">Success Rate</div>
        </div>
    </div>
</section>

<!-- About Section -->
<section style="padding: 6rem 2rem;">
    <div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
        <div>
            <div style="background: #f1f5f9; height: 500px; border-radius: 2rem; position: relative;">
                <!-- Decorative elements -->
                <div style="position: absolute; bottom: -20px; right: -20px; background: var(--primary); width: 150px; height: 150px; border-radius: 1rem; z-index: -1;"></div>
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-weight: 600;">
                    About Us Image
                </div>
            </div>
        </div>
        <div>
            <h4 style="color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 1rem;">About Our School</h4>
            <h2 style="font-size: 3rem; font-weight: 800; color: var(--secondary); margin-bottom: 1.5rem; line-height: 1.2;">Building a Legacy of Knowledge</h2>
            <p style="color: var(--text-light); margin-bottom: 1.5rem; font-size: 1.1rem;">At RMS School, we believe that education is not just about filling a bucket, but lighting a fire. Our holistic approach ensures that every child discovers their unique potential.</p>
            <p style="color: var(--text-light); margin-bottom: 2.5rem; font-size: 1.1rem;">With state-of-the-art facilities and a curriculum designed for the 21st century, we prepare our students to be global citizens and leaders.</p>
            
            <ul style="list-style: none; margin-bottom: 2.5rem;">
                <li style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; color: var(--secondary); font-weight: 500;">
                    <div style="background: #e0e7ff; padding: 0.25rem; border-radius: 50%; color: var(--primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    Interactive Digital Classrooms
                </li>
                <li style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; color: var(--secondary); font-weight: 500;">
                    <div style="background: #e0e7ff; padding: 0.25rem; border-radius: 50%; color: var(--primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    Comprehensive Sports Facilities
                </li>
                <li style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; color: var(--secondary); font-weight: 500;">
                    <div style="background: #e0e7ff; padding: 0.25rem; border-radius: 50%; color: var(--primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    Dedicated STEM Labs
                </li>
            </ul>
            
            <a href="#" class="btn btn-outline" style="padding: 1rem 2rem;">Read More About Us</a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section style="padding: 6rem 2rem; background: #f8fafc;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="text-align: center; margin-bottom: 4rem;">
            <h4 style="color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 1rem;">Our Features</h4>
            <h2 style="font-size: 3rem; color: var(--secondary); margin-bottom: 1rem; font-weight: 800;">Why Parents Choose Us</h2>
            <p style="color: var(--text-light); max-width: 600px; margin: 0 auto; font-size: 1.1rem;">We provide a comprehensive learning environment that focuses on academic excellence and holistic development.</p>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <!-- Feature 1 -->
            <div style="padding: 2.5rem; background: white; border-radius: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: transform 0.3s ease; border: 1px solid #f1f5f9;">
                <div style="width: 60px; height: 60px; background: var(--primary); border-radius: 1rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; color: white; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                </div>
                <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--secondary); font-weight: 700;">Expert Faculty</h3>
                <p style="color: var(--text-light); line-height: 1.7;">Our dedicated team of experienced educators is committed to providing the best learning experience, ensuring every child receives personal attention.</p>
            </div>
            
            <!-- Feature 2 -->
            <div style="padding: 2.5rem; background: white; border-radius: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: transform 0.3s ease; border: 1px solid #f1f5f9;">
                <div style="width: 60px; height: 60px; background: #ec4899; border-radius: 1rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; color: white; box-shadow: 0 10px 15px -3px rgba(236, 72, 153, 0.3);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                </div>
                <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--secondary); font-weight: 700;">Modern Curriculum</h3>
                <p style="color: var(--text-light); line-height: 1.7;">We follow a cutting-edge curriculum designed to prepare students for the challenges of the modern world, integrating technology with traditional values.</p>
            </div>
            
            <!-- Feature 3 -->
            <div style="padding: 2.5rem; background: white; border-radius: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: transform 0.3s ease; border: 1px solid #f1f5f9;">
                <div style="width: 60px; height: 60px; background: #10b981; border-radius: 1rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; color: white; box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                </div>
                <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--secondary); font-weight: 700;">Holistic Growth</h3>
                <p style="color: var(--text-light); line-height: 1.7;">Beyond academics, we focus on sports, arts, and character building to ensure well-rounded development. We nurture the whole child.</p>
            </div>
        </div>
    </div>
</section>

<!-- Latest News Preview -->
<section style="padding: 6rem 2rem;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: end; margin-bottom: 3rem;">
            <div>
                <h4 style="color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 1rem;">Latest Updates</h4>
                <h2 style="font-size: 3rem; color: var(--secondary); font-weight: 800;">News & Events</h2>
            </div>
            <a href="{{ route('public.blog') }}" style="color: var(--primary); font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">View All News <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            @forelse($latestPosts as $post)
            <article style="border-radius: 1.5rem; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); transition: transform 0.3s ease; background: white; display: flex; flex-direction: column;">
                <div style="height: 240px; background-color: #e2e8f0; position: relative; overflow: hidden;">
                    @if($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                    @endif
                </div>
                <div style="padding: 2rem; flex: 1; display: flex; flex-direction: column;">
                    <div style="font-size: 0.875rem; color: var(--primary); font-weight: 600; margin-bottom: 0.5rem;">
                        {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Draft' }}
                    </div>
                    <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--secondary); font-weight: 700; line-height: 1.4;">
                        <a href="{{ route('public.blog.show', $post->slug) }}" style="text-decoration: none; color: inherit;">{{ $post->title }}</a>
                    </h3>
                    <p style="color: var(--text-light); margin-bottom: 1.5rem; flex: 1; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                        {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 100) }}
                    </p>
                    <a href="{{ route('public.blog.show', $post->slug) }}" style="color: var(--primary); font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                        Read More <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </article>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; background: #f8fafc; border-radius: 1rem;">
                <p style="color: var(--text-light); font-size: 1.1rem;">No news updates at the moment. Check back soon!</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Call to Action -->
<section style="background-color: var(--secondary); color: white; padding: 6rem 2rem; text-align: center; position: relative; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(45deg, rgba(79, 70, 229, 0.1) 0%, rgba(0,0,0,0) 100%);"></div>
    <div style="max-width: 800px; margin: 0 auto; position: relative; z-index: 1;">
        <h2 style="font-size: 3rem; margin-bottom: 1.5rem; font-weight: 800;">Ready to Join Our Community?</h2>
        <p style="font-size: 1.25rem; margin-bottom: 3rem; color: var(--text-light); max-width: 600px; margin-left: auto; margin-right: auto;">Admissions are open for the upcoming academic session. Secure your child's future today with RMS School.</p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="{{ route('public.contact') }}" class="btn btn-primary" style="font-size: 1.1rem; padding: 1rem 2.5rem;">Contact Us Now</a>
            <a href="{{ route('public.check-result') }}" class="btn" style="background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); font-size: 1.1rem; padding: 1rem 2.5rem;">Check Results</a>
        </div>
    </div>
</section>
@endsection

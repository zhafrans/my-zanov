<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ZANOV | Premium Footwear Collection</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
      @import url("https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap");

      body {
        font-family: "Poppins", sans-serif;
        background-color: #f9f9f9;
      }

      .hero-section {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
          url("https://images.unsplash.com/photo-1460353581641-37baddab0fa2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1471&q=80");
        background-size: cover;
        background-position: center;
      }

      .title-font {
        font-family: "Playfair Display", serif;
      }

      .shoe-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
          0 10px 10px -5px rgba(0, 0, 0, 0.04);
      }

      .shoe-card {
        transition: all 0.3s ease;
      }

      .category-btn.active {
        background-color: #000;
        color: white;
      }

      .nav-link:hover::after {
        width: 100%;
      }

      .nav-link::after {
        content: "";
        display: block;
        width: 0;
        height: 2px;
        background: #000;
        transition: width 0.3s;
      }

      /* Modal styles */
      .modal {
        transition: opacity 0.3s ease;
      }
    </style>
  </head>
  <body>
    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <a href="{{ route('catalogue') }}" class="flex-shrink-0 flex items-center">
              <span class="title-font text-2xl font-bold text-gray-900">ZANOV</span>
            </a>
          </div>
          <div class="hidden md:ml-6 md:flex md:items-center md:space-x-8">
            <a
              href="#home"
              class="nav-link text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium"
              >Home</a
            >
            <a
              href="#collections"
              class="nav-link text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium"
              >Collections</a
            >
            {{-- <a
              href="#features"
              class="nav-link text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium"
              >Features</a
            > --}}
            <a
              href="#newsletter"
              class="nav-link text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium"
              >Contact</a
            >
          </div>
          <div class="flex items-center">
            <button class="p-2 text-gray-900">
              <i class="fas fa-search"></i>
            </button>
            {{-- <button class="p-2 text-gray-900">
              <i class="fas fa-user"></i>
            </button>
            <button class="p-2 text-gray-900">
              <i class="fas fa-shopping-bag"></i>
            </button> --}}
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-white pt-32 pb-20 md:pt-40 md:pb-28 px-4" id="home">
      <div class="max-w-7xl mx-auto text-center">
        <h1 class="title-font text-4xl md:text-6xl font-bold mb-4" data-aos="fade-down">
          Crafted With Quality, Worn With Pride.
        </h1>
        <p class="text-lg md:text-xl max-w-2xl mx-auto mb-8" data-aos="fade-down" data-aos-delay="100">
          Discover the perfect blend of comfort and style with ZANOV's premium
          footwear collection.
        </p>
        <a
          href="#collections"
          class="bg-white text-black px-8 py-3 font-medium hover:bg-gray-100 transition duration-300"
          data-aos="fade-up" data-aos-delay="200"
        >
          Shop Now
        </a>
      </div>
    </section>

    <!-- Categories -->
    <section class="max-w-7xl mx-auto px-4 py-12" id="collections">
      <div class="flex flex-wrap justify-center gap-4 mb-12">
        <button
          class="category-btn active px-6 py-2 rounded-full border border-black text-sm font-medium transition duration-300"
          data-category="all"
        >
          All
        </button>
        <button
          class="category-btn px-6 py-2 rounded-full border border-black text-sm font-medium transition duration-300"
          data-category="man"
        >
          Men
        </button>
        <button
          class="category-btn px-6 py-2 rounded-full border border-black text-sm font-medium transition duration-300"
          data-category="woman"
        >
          Women
        </button>
        @foreach($heels as $heel)
        <button
          class="category-btn px-6 py-2 rounded-full border border-black text-sm font-medium transition duration-300"
          data-category="heel-{{ $heel->id }}"
        >
          {{ $heel->name }}
        </button>
        @endforeach
      </div>

       <!-- search -->
      <div class="flex justify-center mb-8">
          <form action="{{ route('catalogue') }}" method="GET" class="w-full max-w-md">
              <div class="relative">
                  <input
                      type="text"
                      name="search"
                      placeholder="Search ZANOV product..."
                      value="{{ request('search') }}"
                      class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent"
                  />
                  <button
                      type="submit"
                      class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                  >
                      <i class="fas fa-search"></i>
                  </button>
              </div>
          </form>
      </div>

      <h2 class="title-font text-3xl font-bold text-center mb-12" data-aos="fade-up">
        Our Collections
      </h2>


      <!-- Product Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="product-grid">
        @foreach($variants as $variant)
          <div
            class="shoe-card bg-white rounded-lg overflow-hidden shadow-md"
            data-category="heel-{{ $variant->heel_id }} {{ $variant->gender }}"
            data-aos="fade-up"
            data-aos-delay="{{ ($loop->index % 3) * 100 }}"
          >
          <div class="relative overflow-hidden h-80">
            <img
              src="{{ $variant->image ? Storage::url($variant->image) : 'https://via.placeholder.com/300x400?text=No+Image' }}"
              alt="{{ $variant->product->name }}"
              class="w-full h-full object-cover"
            />
            <div
              class="absolute inset-0 bg-black bg-opacity-0 flex items-center justify-center opacity-0 hover:opacity-100 hover:bg-opacity-20 transition duration-300"
            >
              <button 
                class="quick-view-btn bg-white text-black px-6 py-2 font-medium"
                data-image="{{ $variant->image ? Storage::url($variant->image) : 'https://via.placeholder.com/300x400?text=No+Image' }}"
                data-name="{{ $variant->product->name }}"
              >
                Quick View
              </button>
            </div>
            @if($loop->first)
            <span
              class="absolute top-4 right-4 bg-white px-3 py-1 text-xs font-medium"
              >New</span
            >
            @endif
          </div>
          <div class="p-4">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="font-medium text-gray-900">{{ $variant->product->name }}</h3>
                <p class="text-gray-500 text-sm">{{ $variant->heel->name }} Collection</p>
              </div>
              <span class="font-medium text-gray-900">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
            </div>
            <div class="mt-4 flex justify-between items-center">
              <div class="flex space-x-1">
                <span class="w-4 h-4 rounded-full" style="background-color: {{ $variant->color->code ?? '#000' }}"></span>
              </div>
              <div class="flex items-center">
                <i class="fas fa-star text-yellow-400"></i>
                <span class="text-sm ml-1">4.8</span>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      <div class="text-center mt-12">
        {{ $variants->links() }}
      </div>
    </section>

    <!-- Features -->
    <section class="bg-gray-100 py-16">
      <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          
          <!-- 100% Guarantee -->
          <div class="text-center" data-aos="fade-up" data-aos-delay="0">
            <div class="flex justify-center mb-4">
              <i class="fas fa-shield-alt text-3xl"></i> <!-- Shield icon for guarantee -->
            </div>
            <h3 class="font-medium text-lg mb-2">100% Guarantee</h3>
            <p class="text-gray-600">Shop with confidence — your satisfaction is our priority.</p>
          </div>
          
          <!-- Best Quality -->
          <div class="text-center" data-aos="fade-up" data-aos-delay="100">
            <div class="flex justify-center mb-4">
              <i class="fas fa-star text-3xl"></i> <!-- Star icon for quality -->
            </div>
            <h3 class="font-medium text-lg mb-2">Best Quality</h3>
            <p class="text-gray-600">Premium materials and craftsmanship in every product.</p>
          </div>
          
          <!-- Easy Payment -->
          <div class="text-center" data-aos="fade-up" data-aos-delay="200">
            <div class="flex justify-center mb-4">
              <i class="fas fa-credit-card text-3xl"></i> <!-- Credit card icon for payment -->
            </div>
            <h3 class="font-medium text-lg mb-2">Easy Payment</h3>
            <p class="text-gray-600">Pay in full or installments — with flexible pricing options.</p>
          </div>

        </div>
      </div>
    </section>

    <!-- Newsletter -->
    <section class="max-w-7xl mx-auto px-4 py-16" id="newsletter">
      <div class="bg-black text-white p-8 md:p-12 rounded-lg">
        <div class="max-w-2xl mx-auto text-center">
          <h2 class="title-font text-3xl font-bold mb-4">Stay Updated</h2>
          <p class="mb-6">
            Subscribe to our newsletter for the latest collections, exclusive
            offers, and style inspiration.
          </p>
          <div class="flex flex-col sm:flex-row gap-2">
            <input
              type="email"
              placeholder="Your email address"
              class="flex-grow px-4 py-3 text-gray-900 rounded"
            />
            <button
              class="bg-white text-black px-6 py-3 font-medium hover:bg-gray-200 transition duration-300 rounded"
            >
              Subscribe
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-12 pb-6">
      <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
          <div>
            <h3 class="title-font text-xl font-bold mb-4">ZANOV</h3>
            <p class="text-gray-400">
              Since 2022, ZANOV has been crafting high-quality casual and formal footwear in a wide range of styles and colors. Proudly handmade with care, our shoes combine comfort, durability, and refined design for everyday elegance.
            </p>
          </div>
          <div>
            <h4 class="font-medium mb-4">Shop</h4>
            <ul class="space-y-2">
              <li>
                <a href="{{ route('catalogue') }}" class="text-gray-400 hover:text-white transition"
                  >All Products</a
                >
              </li>
              <li>
                <a href="{{ route('catalogue', ['gender' => 'man']) }}" class="text-gray-400 hover:text-white transition"
                  >Men</a
                >
              </li>
              <li>
                <a href="{{ route('catalogue', ['gender' => 'woman']) }}" class="text-gray-400 hover:text-white transition"
                  >Women</a
                >
              </li>
            </ul>
          </div>
          <div>
            <h4 class="font-medium mb-4">Customer Service</h4>
            <ul class="space-y-2">
              <li>
                <a href="https://wa.me/6281329235551" target="_blank" class="text-gray-400 hover:text-white transition">
                  Contact Us
                </a>
              </li>
              {{-- <li>
                <a href="#" class="text-gray-400 hover:text-white transition"
                  >FAQs</a
                >
              </li>
              <li>
                <a href="#" class="text-gray-400 hover:text-white transition"
                  >Shipping & Returns</a
                >
              </li> --}}
              <li>
                <a href="#" class="text-gray-400 hover:text-white transition"
                  >Size Guide</a
                >
              </li>
            </ul>
          </div>
          <div>
            <h4 class="font-medium mb-4">Connect</h4>
            <div class="flex space-x-4 mb-4">
              <a href="#" class="text-gray-400 hover:text-white transition"
                ><i class="fab fa-facebook-f"></i
              ></a>
              <a href="https://www.instagram.com/zanovshoes" target="_blank" class="text-gray-400 hover:text-white transition">
                <i class="fab fa-instagram"></i>
              </a>
              <a href="#" class="text-gray-400 hover:text-white transition"
                ><i class="fab fa-twitter"></i
              ></a>
              <a href="#" class="text-gray-400 hover:text-white transition"
                ><i class="fab fa-pinterest"></i
              ></a>
            </div>
            <p class="text-gray-400">Email: bintangstorepwt@gmail.com</p>
            <p class="text-gray-400">Phone: +62 8132 9235 551</p>
          </div>
        </div>
        <div
          class="border-t border-gray-800 pt-6 flex flex-col md:flex-row justify-between items-center"
        >
          <p class="text-gray-400 text-sm">
            © {{ date('Y') }} ZANOV. All rights reserved.
          </p>
          <div class="flex space-x-6 mt-4 md:mt-0">
            <a href="#" class="text-gray-400 hover:text-white text-sm"
              >Privacy Policy</a
            >
            <a href="#" class="text-gray-400 hover:text-white text-sm"
              >Terms of Service</a
            >
            <a href="#" class="text-gray-400 hover:text-white text-sm"
              >Cookie Policy</a
            >
          </div>
        </div>
      </div>
    </footer>

    <!-- Image Preview Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        
        <!-- Modal content -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modalTitle"></h3>
                <div class="mt-2">
                  <img id="modalImage" src="" alt="" class="w-full h-auto max-h-[70vh] object-contain">
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button" class="close-modal mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <script>
      // Category filter functionality
      document.addEventListener("DOMContentLoaded", function () {
        const categoryButtons = document.querySelectorAll(".category-btn");
        const productCards = document.querySelectorAll(".shoe-card");

        categoryButtons.forEach((button) => {
          button.addEventListener("click", function () {
            // Remove active class from all buttons
            categoryButtons.forEach((btn) => btn.classList.remove("active"));

            // Add active class to clicked button
            this.classList.add("active");

            const category = this.getAttribute("data-category");

            // Filter products
            productCards.forEach((card) => {
              const cardCategories = card.getAttribute("data-category").split(' ');
              
              if (
                category === "all" ||
                cardCategories.includes(category)
              ) {
                card.style.display = "block";
              } else {
                card.style.display = "none";
              }
            });
          });
        });

        // Add hover effect to shoe cards
        productCards.forEach((card) => {
          card.addEventListener("mouseenter", function () {
            this.style.transform = "translateY(-10px)";
            this.style.boxShadow =
              "0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)";
          });

          card.addEventListener("mouseleave", function () {
            this.style.transform = "translateY(0)";
            this.style.boxShadow = "";
          });
        });

        // Quick View Modal functionality
        const quickViewBtns = document.querySelectorAll('.quick-view-btn');
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('modalTitle');
        const closeModal = document.querySelector('.close-modal');

        quickViewBtns.forEach(btn => {
          btn.addEventListener('click', function() {
            const imageUrl = this.getAttribute('data-image');
            const productName = this.getAttribute('data-name');
            
            modalImage.src = imageUrl;
            modalImage.alt = productName;
            modalTitle.textContent = productName;
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
          });
        });

        closeModal.addEventListener('click', function() {
          modal.classList.add('hidden');
          document.body.style.overflow = 'auto';
        });

        // Close modal when clicking outside the image
        modal.addEventListener('click', function(e) {
          if (e.target === modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
          }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
          if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
          }
        });
      });
    </script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
      // Initialize AOS
      AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
      });

      // Smooth scrolling for navigation links
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
          e.preventDefault();
          
          const targetId = this.getAttribute('href');
          if (targetId === '#') return;
          
          const targetElement = document.querySelector(targetId);
          if (targetElement) {
            targetElement.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
            
            // Update URL without refreshing
            history.pushState(null, null, targetId);
          }
        });
      });
    </script>
  </body>
</html>
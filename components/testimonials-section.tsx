import Link from "next/link";
import { Star, Check } from "lucide-react";
import { testimonials, testimonialFeatures } from "@/lib/data";

export function TestimonialsSection() {
  // Split testimonials into two columns
  const midpoint = Math.ceil(testimonials.length / 2);
  const column1 = testimonials.slice(0, midpoint);
  const column2 = testimonials.slice(midpoint);

  return (
    <section id="testimonials" className="py-12 lg:py-20">
      <div className="mx-auto max-w-7xl px-4 lg:px-8">
        <div className="flex flex-col gap-12 lg:flex-row lg:items-start lg:gap-12">
          {/* Left Column - Intro */}
          <div className="flex flex-col gap-8 lg:sticky lg:top-24 lg:max-w-md">
            {/* Trustpilot Badge */}
            <div className="flex items-center gap-2">
              {[...Array(5)].map((_, i) => (
                <Star
                  key={i}
                  className="h-6 w-6 fill-primary-400 text-primary-400"
                />
              ))}
              <span className="ml-2 font-medium text-white">Excellent</span>
            </div>

            <h2 className="text-3xl font-light uppercase text-white lg:text-4xl">
              5,000+ happy traders
            </h2>

            <p className="text-lg font-medium text-gray-400 lg:text-xl">
              And over 3,000 decided to buy again, because of:
            </p>

            {/* Features */}
            <ul className="flex flex-col gap-3">
              {testimonialFeatures.map((feature, index) => (
                <li key={index} className="flex items-center gap-2">
                  <Check className="h-6 w-6 flex-shrink-0 text-primary-400" />
                  <span className="text-gray-400">{feature}</span>
                </li>
              ))}
            </ul>

            <Link
              href="#pricing"
              className="inline-flex w-full justify-center rounded-lg bg-primary-400 px-6 py-4 text-base font-medium uppercase text-gray-950 transition-colors hover:bg-primary-500 lg:w-auto"
            >
              GET FUNDED NOW
            </Link>
          </div>

          {/* Right Column - Testimonials Grid */}
          <div className="flex flex-1 gap-4 overflow-hidden lg:max-h-[744px]">
            <div className="flex flex-1 flex-col gap-4">
              {column1.map((testimonial, index) => (
                <TestimonialCard key={index} testimonial={testimonial} />
              ))}
            </div>
            <div className="hidden flex-1 flex-col gap-4 md:flex">
              {column2.map((testimonial, index) => (
                <TestimonialCard key={index} testimonial={testimonial} />
              ))}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

interface TestimonialData {
  text: string;
  author: {
    name: string;
    country: string;
    initials: string;
  };
}

function TestimonialCard({ testimonial }: { testimonial: TestimonialData }) {
  return (
    <div className="flex flex-col gap-8 rounded-2xl bg-gray-800 p-8">
      {/* Stars */}
      <div className="flex gap-1">
        {[...Array(5)].map((_, i) => (
          <Star
            key={i}
            className="h-5 w-5 fill-primary-400 text-primary-400"
          />
        ))}
      </div>

      {/* Quote */}
      <p className="text-base font-medium leading-relaxed text-gray-400">
        &quot;{testimonial.text}&quot;
      </p>

      {/* Author */}
      <div className="flex items-center gap-3">
        <div className="flex h-16 w-16 items-center justify-center rounded-full bg-primary-400 text-2xl font-light uppercase text-gray-950">
          {testimonial.author.initials}
        </div>
        <div>
          <div className="font-medium text-white">{testimonial.author.name}</div>
          <div className="text-gray-400">{testimonial.author.country}</div>
        </div>
      </div>
    </div>
  );
}

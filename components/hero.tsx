"use client";

import Link from "next/link";
import { Zap, Star, ChevronLeft, ChevronRight } from "lucide-react";
import { heroFeatures } from "@/lib/data";
import useEmblaCarousel from "embla-carousel-react";
import Autoplay from "embla-carousel-autoplay";
import { useCallback } from "react";

export function Hero() {
  const [emblaRef, emblaApi] = useEmblaCarousel({ loop: true }, [
    Autoplay({ delay: 4000, stopOnInteraction: false }),
  ]);

  const scrollPrev = useCallback(() => {
    if (emblaApi) emblaApi.scrollPrev();
  }, [emblaApi]);

  const scrollNext = useCallback(() => {
    if (emblaApi) emblaApi.scrollNext();
  }, [emblaApi]);

  return (
    <section id="hero-section" className="relative overflow-hidden py-12 lg:py-20">
      <div className="mx-auto max-w-7xl px-4 lg:px-8">
        <div className="flex flex-col gap-12 lg:flex-row lg:items-center lg:gap-16">
          {/* Content */}
          <div className="flex flex-1 flex-col gap-8">
            {/* Promo Badge */}
            <div className="inline-flex w-fit items-center gap-2 rounded-full border-2 border-secondary-500 py-1.5 pl-1.5 pr-4">
              <span className="rounded-full bg-secondary-500 px-3 py-2 text-sm font-medium text-gray-950">
                TRADE BIG
              </span>
              <span className="text-sm font-bold text-white">
                Reach Your Next Level.
              </span>
            </div>

            {/* Title */}
            <h1 className="text-4xl font-light uppercase leading-tight text-white lg:text-5xl">
              Supercharge your trading with up to{" "}
              <span className="text-primary-400">$750K</span> in funding
            </h1>

            {/* Features List */}
            <ul className="flex flex-col gap-3">
              {heroFeatures.map((feature, index) => (
                <li key={index} className="flex items-center gap-2">
                  <Zap className="h-5 w-5 flex-shrink-0 text-primary-400" />
                  <span className="text-lg font-medium text-gray-400 lg:text-xl">
                    {feature}
                  </span>
                </li>
              ))}
            </ul>

            {/* CTA Button */}
            <div>
              <Link
                href="#pricing"
                className="inline-flex items-center gap-3 rounded-lg bg-primary-400 px-6 py-4 text-xl font-medium uppercase text-gray-900 transition-colors hover:bg-primary-500"
              >
                <Zap className="h-8 w-8" />
                Get funded now
              </Link>
            </div>

            {/* Trustpilot */}
            <TrustpilotBadge />
          </div>

          {/* Hero Image Carousel */}
          <div className="flex-1">
            <div className="relative">
              {/* Glow Effect */}
              <div className="absolute left-1/2 top-1/2 -z-10 h-[300px] w-[450px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-error-400/30 blur-[100px]" />

              {/* Carousel */}
              <div className="overflow-hidden rounded-2xl" ref={emblaRef}>
                <div className="flex">
                  <div className="min-w-0 flex-[0_0_100%]">
                    <div className="flex aspect-[4/3] items-center justify-center rounded-2xl bg-gradient-to-br from-gray-800 to-gray-900 p-8">
                      <div className="text-center">
                        <div className="mb-4 text-6xl font-light text-primary-400">$750K</div>
                        <div className="text-xl text-gray-400">Maximum Funding</div>
                      </div>
                    </div>
                  </div>
                  <div className="min-w-0 flex-[0_0_100%]">
                    <div className="flex aspect-[4/3] items-center justify-center rounded-2xl bg-gradient-to-br from-gray-800 to-gray-900 p-8">
                      <div className="text-center">
                        <div className="mb-4 text-6xl font-light text-secondary-400">90%</div>
                        <div className="text-xl text-gray-400">Profit Split</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              {/* Carousel Controls */}
              <div className="mt-4 flex items-center justify-center gap-4">
                <button
                  onClick={scrollPrev}
                  className="flex h-10 w-10 items-center justify-center rounded-full border border-gray-700 bg-gray-800 transition-colors hover:border-gray-600"
                  aria-label="Previous slide"
                >
                  <ChevronLeft className="h-5 w-5 text-white" />
                </button>
                <button
                  onClick={scrollNext}
                  className="flex h-10 w-10 items-center justify-center rounded-full border border-gray-700 bg-gray-800 transition-colors hover:border-gray-600"
                  aria-label="Next slide"
                >
                  <ChevronRight className="h-5 w-5 text-white" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

function TrustpilotBadge() {
  return (
    <div className="flex items-center gap-3">
      <div className="flex items-center gap-1">
        {[...Array(5)].map((_, i) => (
          <Star
            key={i}
            className="h-5 w-5 fill-primary-400 text-primary-400"
          />
        ))}
      </div>
      <div className="flex flex-col">
        <span className="text-sm font-medium text-white">Excellent</span>
        <span className="text-xs text-gray-400">Based on Trustpilot reviews</span>
      </div>
    </div>
  );
}

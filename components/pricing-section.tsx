"use client";

import { useState } from "react";
import Link from "next/link";
import { Zap, Star, Wallet, DollarSign, Check } from "lucide-react";
import {
  marketTypes,
  accountTypes,
  pricingPlans,
  pricingTestimonials,
} from "@/lib/data";
import { cn } from "@/lib/utils";

export function PricingSection() {
  const [selectedMarket, setSelectedMarket] = useState(marketTypes[0].slug);
  const [selectedAccountType, setSelectedAccountType] = useState(
    accountTypes[0].slug
  );

  const currentAccountType = accountTypes.find(
    (at) => at.slug === selectedAccountType
  );

  return (
    <section id="pricing" className="py-12 lg:py-20">
      <div className="mx-auto max-w-7xl px-4 lg:px-8">
        {/* Header */}
        <header className="text-center">
          <h2 className="text-3xl font-light uppercase text-white lg:text-4xl">
            Choose <span className="text-primary-400">account</span> type
          </h2>
          <p className="mx-auto mt-4 max-w-3xl text-lg font-light text-gray-400 lg:text-xl">
            Choose from flexible account sizes and plans tailored to your
            trading style—whether you&apos;re growing your skills or ready to trade
            real capital with confidence.
          </p>
        </header>

        {/* Market Type Selector */}
        <div className="mt-8 flex justify-center">
          <div className="inline-flex gap-1 rounded-xl bg-gray-800 p-1">
            {marketTypes.map((market) => (
              <button
                key={market.slug}
                onClick={() => setSelectedMarket(market.slug)}
                className={cn(
                  "rounded-lg px-6 py-3 text-base font-medium uppercase transition-colors",
                  selectedMarket === market.slug
                    ? "bg-primary-400 text-gray-950"
                    : "text-primary-400 hover:bg-gray-700"
                )}
              >
                {market.name}
              </button>
            ))}
          </div>
        </div>

        {/* Account Type Tabs */}
        <div className="mt-8 flex flex-col items-center gap-4 lg:flex-row lg:justify-center">
          <div className="flex gap-2">
            {accountTypes.map((type) => (
              <button
                key={type.slug}
                onClick={() => setSelectedAccountType(type.slug)}
                className={cn(
                  "rounded-full border-2 px-6 py-3 text-sm font-medium uppercase transition-colors",
                  selectedAccountType === type.slug
                    ? "border-primary-400 bg-primary-400 text-gray-950"
                    : "border-gray-700 text-gray-400 hover:border-gray-600"
                )}
              >
                {type.name}
              </button>
            ))}
          </div>
        </div>

        {/* Benefits Row */}
        {currentAccountType && (
          <div className="mt-6 flex flex-wrap items-center justify-center gap-4">
            {currentAccountType.benefits.map((benefit, index) => (
              <div key={index} className="flex items-center gap-2">
                {index > 0 && (
                  <Zap className="h-5 w-5 text-primary-400" />
                )}
                <span className="text-sm font-medium text-gray-400">
                  {benefit}
                </span>
              </div>
            ))}
          </div>
        )}

        {/* Pricing Cards */}
        <div className="mt-12 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
          {pricingPlans.map((plan) => (
            <PricingCard key={plan.id} plan={plan} />
          ))}
        </div>

        {/* Testimonials */}
        <div className="mt-12 grid gap-4 md:grid-cols-2">
          {pricingTestimonials.map((testimonial, index) => (
            <TestimonialCard key={index} testimonial={testimonial} />
          ))}
        </div>

        {/* Competition/Join Section */}
        <CompetitionSection />
      </div>
    </section>
  );
}

interface PricingPlan {
  id: number;
  size: string;
  price: number;
  isMostPopular: boolean;
  metaInfo: {
    profitTarget: string;
    maxDrawdown: string;
    dailyDrawdown: string;
    minTradingDays: string;
    profitSplit: string;
  };
}

function PricingCard({ plan }: { plan: PricingPlan }) {
  return (
    <div
      className={cn(
        "relative flex flex-col rounded-2xl border p-6",
        plan.isMostPopular
          ? "border-primary-400 bg-gradient-to-b from-primary-400/10 to-transparent"
          : "border-gray-700 bg-gray-800"
      )}
    >
      {/* Most Popular Badge */}
      {plan.isMostPopular && (
        <div className="absolute -top-3 left-1/2 -translate-x-1/2">
          <div className="flex items-center gap-2 rounded-full bg-primary-400 px-4 py-1.5">
            <Zap className="h-4 w-4 text-gray-950" />
            <span className="text-sm font-medium text-gray-950">
              Most popular
            </span>
          </div>
        </div>
      )}

      {/* Plan Size */}
      <div className="mt-2 text-center">
        <h3 className="text-xl font-medium text-white">{plan.size} Account</h3>
      </div>

      {/* Price */}
      <div className="mt-6 text-center">
        <div className="text-4xl font-light text-white">${plan.price}</div>
        <div className="mt-1 text-sm text-gray-400">per month</div>
      </div>

      {/* Meta Info */}
      <div className="mt-6 flex flex-col gap-3">
        <MetaRow label="Profit Target" value={plan.metaInfo.profitTarget} />
        <MetaRow label="Max Drawdown" value={plan.metaInfo.maxDrawdown} />
        <MetaRow label="Daily Drawdown" value={plan.metaInfo.dailyDrawdown} />
        <MetaRow label="Min Trading Days" value={plan.metaInfo.minTradingDays} />
        <MetaRow label="Profit Split" value={plan.metaInfo.profitSplit} />
      </div>

      {/* CTA */}
      <Link
        href="#"
        className={cn(
          "mt-6 flex w-full items-center justify-center gap-2 rounded-lg py-3 text-sm font-medium uppercase transition-colors",
          plan.isMostPopular
            ? "bg-primary-400 text-gray-950 hover:bg-primary-500"
            : "border border-gray-600 text-white hover:bg-gray-700"
        )}
      >
        <Zap className="h-5 w-5" />
        GET FUNDED WITH ${plan.size}
      </Link>
    </div>
  );
}

function MetaRow({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex items-center justify-between">
      <span className="text-sm text-gray-400">{label}</span>
      <span className="text-sm font-medium text-white">{value}</span>
    </div>
  );
}

interface Testimonial {
  quote: string;
  name: string;
  country: string;
  role: string;
}

function TestimonialCard({ testimonial }: { testimonial: Testimonial }) {
  return (
    <div className="flex overflow-hidden rounded-2xl bg-gray-850">
      <div className="w-48 flex-shrink-0 bg-gradient-to-br from-gray-700 to-gray-800" />
      <div className="flex flex-col gap-4 p-8">
        <p className="line-clamp-2 text-base text-gray-400">
          {testimonial.quote}
        </p>
        <div>
          <div className="flex items-center gap-2">
            <span className="text-base font-medium text-white">
              {testimonial.name}, {testimonial.country}
            </span>
            <Star className="h-5 w-5 fill-primary-400 text-primary-400" />
          </div>
          <div className="text-base text-gray-400">{testimonial.role}</div>
        </div>
      </div>
    </div>
  );
}

function CompetitionSection() {
  return (
    <div className="mt-12 overflow-hidden rounded-2xl bg-gradient-to-b from-gray-800 to-gray-950 p-8 lg:p-12">
      <div className="flex flex-col gap-8 lg:flex-row lg:items-center lg:gap-12">
        {/* Content */}
        <div className="flex flex-1 flex-col gap-8">
          <h2 className="text-3xl font-light uppercase tracking-tight text-white lg:text-4xl">
            JOIN THE FASTEST GROWING FIRM
          </h2>

          {/* Reward Cards */}
          <div className="flex flex-col gap-4 sm:flex-row">
            <div className="flex-1 rounded-2xl bg-gray-800 p-6">
              <div className="flex items-center gap-2">
                <Wallet className="h-6 w-6 text-primary-400" />
                <span className="text-xl font-bold text-primary-400">
                  1 HOUR PAYOUTS
                </span>
              </div>
              <p className="mt-2 text-gray-400">Average Processing Time</p>
            </div>
            <div className="flex-1 rounded-2xl bg-gray-800 p-6">
              <div className="flex items-center gap-2">
                <DollarSign className="h-6 w-6 text-primary-400" />
                <span className="text-xl font-bold text-primary-400">
                  90% PROFIT SHARE
                </span>
              </div>
              <p className="mt-2 text-gray-400">Earn More From Every Trade</p>
            </div>
          </div>

          <p className="text-lg font-medium text-gray-400 lg:text-xl">
            Experience instant withdrawals, verified through RiseWorks, and
            enjoy full transparency from challenge to payout.
          </p>

          {/* Perks */}
          <div className="flex flex-col gap-3">
            <div className="flex items-center gap-2">
              <Check className="h-6 w-6 text-primary-400" />
              <span className="text-gray-400">
                Trade, Profit, Withdraw — Instantly
              </span>
            </div>
            <div className="flex items-center gap-2">
              <Check className="h-6 w-6 text-primary-400" />
              <span className="text-gray-400">
                Available across all of our plans
              </span>
            </div>
          </div>

          <div>
            <Link
              href="#pricing"
              className="inline-flex rounded-lg bg-primary-400 px-6 py-3 text-base font-medium uppercase text-gray-950 transition-colors hover:bg-primary-500"
            >
              Get Funded Now
            </Link>
          </div>
        </div>

        {/* Image Placeholder */}
        <div className="flex-1">
          <div className="flex aspect-square items-center justify-center rounded-2xl bg-gray-800">
            <div className="text-center">
              <DollarSign className="mx-auto h-24 w-24 text-primary-400 opacity-50" />
              <div className="mt-4 text-2xl font-light text-gray-500">USD</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

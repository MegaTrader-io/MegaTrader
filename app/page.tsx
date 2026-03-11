import { Navbar } from "@/components/navbar";
import { Hero } from "@/components/hero";
import { HowItWorks } from "@/components/how-it-works";
import { VerifiedSection } from "@/components/verified-section";
import { PricingSection } from "@/components/pricing-section";
import { TestimonialsSection } from "@/components/testimonials-section";
import { SocialSection } from "@/components/social-section";
import { FAQSection } from "@/components/faq-section";
import { Footer } from "@/components/footer";

export default function Home() {
  return (
    <div className="min-h-screen bg-gray-950">
      <Navbar />
      <main>
        <Hero />
        <HowItWorks />
        <VerifiedSection />
        <PricingSection />
        <TestimonialsSection />
        <SocialSection />
        <FAQSection />
      </main>
      <Footer />
    </div>
  );
}

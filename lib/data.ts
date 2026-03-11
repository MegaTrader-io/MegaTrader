// Hero Section Data
export const heroFeatures = [
  "Start a challenge and get instant funding",
  "Lightning fast payouts in just a few hours",
  "Journal to track and improve your trading",
];

// How It Works / Payout Steps
export const payoutSteps = [
  {
    title: "Sign up",
    description: "Go Funded, or pick and pass a Challenge.",
  },
  {
    title: "Trade 7 days",
    description: "Scale your trading with your simulated funds.",
  },
  {
    title: "Get paid",
    description: "Request your payout. We'll pay in ~1 hour.",
  },
];

// Pricing Plans
export const marketTypes = [
  { slug: "forex", name: "Forex" },
  { slug: "futures", name: "Futures" },
];

export const accountTypes = [
  {
    slug: "challenge",
    name: "Challenge",
    benefits: [
      "2-Phase evaluation",
      "Up to 90% profit split",
      "Scale up to $2M",
    ],
  },
  {
    slug: "funded-plan",
    name: "Instant Funding",
    benefits: [
      "No evaluation required",
      "Start trading immediately",
      "90% profit split",
    ],
  },
];

export const pricingPlans = [
  {
    id: 1,
    size: "10K",
    price: 99,
    isMostPopular: false,
    metaInfo: {
      profitTarget: "8%",
      maxDrawdown: "8%",
      dailyDrawdown: "4%",
      minTradingDays: "5 days",
      profitSplit: "80%",
    },
  },
  {
    id: 2,
    size: "25K",
    price: 199,
    isMostPopular: false,
    metaInfo: {
      profitTarget: "8%",
      maxDrawdown: "8%",
      dailyDrawdown: "4%",
      minTradingDays: "5 days",
      profitSplit: "80%",
    },
  },
  {
    id: 3,
    size: "50K",
    price: 299,
    isMostPopular: true,
    metaInfo: {
      profitTarget: "8%",
      maxDrawdown: "10%",
      dailyDrawdown: "5%",
      minTradingDays: "5 days",
      profitSplit: "85%",
    },
  },
  {
    id: 4,
    size: "100K",
    price: 499,
    isMostPopular: false,
    metaInfo: {
      profitTarget: "8%",
      maxDrawdown: "10%",
      dailyDrawdown: "5%",
      minTradingDays: "5 days",
      profitSplit: "90%",
    },
  },
];

// Testimonials in pricing section
export const pricingTestimonials = [
  {
    quote:
      "The rules are fair and easy to follow. Everything's clear, and the platform feels built for traders.",
    name: "Daniel Ruiz",
    country: "United States",
    role: "Professional Trader",
  },
  {
    quote:
      "Got my payout within an hour — no delays, no confusion. Super smooth process.",
    name: "Ava Thompson",
    country: "Canada",
    role: "Professional Trader",
  },
];

// Verified Achievements
export const verifiedCertificates = [
  { title: "Mega Certified Trader - Passed" },
  { title: "Mega Certified Trader - Passed" },
  { title: "Mega Certified Trader - Withdrawal" },
  { title: "Mega Certified Trader - Withdrawal" },
];

// Testimonials Section
export const testimonials = [
  {
    text: "Education is the passport to the future, for tomorrow belongs to those who prepare for it today.",
    author: { name: "Angela Kim", country: "US", initials: "AK" },
  },
  {
    text: "Excellent Support and very fast with the payouts I have another funded account with another prop-firms but MEGA TRADER let me totally impressed with every little thing…!!",
    author: { name: "Denskyn", country: "US", initials: "DE" },
  },
  {
    text: "The best propfirm really loving it currently on evals and their platform is being upgraded. CS is the best as well and the ceo is always there to answer any questions I have. I highly recommend this prop firm",
    author: { name: "Joe", country: "US", initials: "JO" },
  },
  {
    text: "The only thing that holds you back is the story you tell yourself about why you can't.",
    author: { name: "Robert Fox", country: "US", initials: "RF" },
  },
  {
    text: "The only thing that will redeem mankind is cooperation.",
    author: { name: "Cody Fisher", country: "US", initials: "CF" },
  },
  {
    text: "Great firm. Agents willing to help and CEO always making sure you are satisfy with the services. FAST payouts onces is approved. I will continue using this firm for my trading career.",
    author: { name: "Aldo Ramírez", country: "US", initials: "AR" },
  },
  {
    text: "As a new company I was skeptical but yes I received a payout and I'm happy overall. The rules are not too complicated. They have a few more rules than other firms but also offer EOD drawdown and the lowest price on a funded account I've ever seen that's not a scam.",
    author: { name: "TG", country: "US", initials: "TG" },
  },
  {
    text: "We are not human beings having a spiritual experience; we are spiritual beings having a human experience.",
    author: { name: "Jerome Bell", country: "US", initials: "JB" },
  },
  {
    text: "Great pricing compared to other prop firms. Clear rules, fair structure, and no unnecessary complexity.",
    author: { name: "Leo", country: "US", initials: "LE" },
  },
  {
    text: "MegaTrader stands out for its organization. From account setup to daily trading, everything was clearly laid out, which allowed me to focus on trading instead of worrying about unclear rules.",
    author: { name: "Grayson Wilson", country: "US", initials: "GW" },
  },
  {
    text: "The evaluation process felt fair and well designed. Performance metrics were easy to follow, and the platform tools helped me manage risk properly throughout my trading sessions.",
    author: { name: "Alex Watkins", country: "US", initials: "AW" },
  },
  {
    text: "What I appreciated most about MegaTrader was the clarity around expectations. Nothing felt hidden or confusing, and the documentation answered most of my questions before I even needed to contact support.",
    author: { name: "Daniel Wright", country: "US", initials: "DW" },
  },
];

export const testimonialFeatures = [
  "Instant simulated funding",
  "Fastest customer service",
  "Clear and straightforward",
];

// Social Media Links
export const socialLinks = [
  { slug: "discord", name: "Discord", url: "https://discord.com/invite/megatrader" },
  { slug: "instagram", name: "Instagram", url: "https://www.instagram.com/megatrader.io/" },
  { slug: "facebook", name: "Facebook", url: "#" },
  { slug: "x", name: "X (Twitter)", url: "https://x.com/MegaTrader_io" },
];

// Social Section Features
export const socialFeatures = [
  "Free built-in journal",
  "Get Instant Funding or take a Challenge.",
  "Automated payouts",
];

// FAQ Data
export const faqs = [
  {
    question: "How do I sign up for an account?",
    answer:
      "Choose your plan, create an account using your email, and get immediate access to your evaluation account.",
  },
  {
    question: "What do I need to pass the evaluation?",
    answer:
      "Maintain the minimum trading days and reach the profit target while respecting the maximum drawdown limits.",
  },
  {
    question: "Can I start right after payment?",
    answer:
      "Yes, once the payment is confirmed, your credentials will be sent to your email immediately.",
  },
  {
    question: "Is this suitable for beginners?",
    answer:
      "While we provide tools and support, basic knowledge of trading and risk management is highly recommended.",
  },
];

// Navigation Links
export const navLinks = [
  { href: "#how-it-works", label: "How It Works" },
  { href: "#pricing", label: "Pricing" },
  { href: "#testimonials", label: "Testimonials" },
  { href: "#faqs", label: "FAQs" },
];

// Footer Links
export const footerLinks = [
  { label: "Disclaimer", href: "#" },
  { label: "Privacy Policy", href: "#" },
  { label: "Terms of Service", href: "#" },
  { label: "Cookies Settings", href: "#" },
];

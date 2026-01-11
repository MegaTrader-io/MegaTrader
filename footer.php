<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package megatrader
 */
?>


<!-- Copy Right Area Start -->
<div class="footer-wrapper footer-sitcky d-none">
    <div class="container">
        <div class="footer-logo">
            <a target="_blank" href="https://www.megatraderfutures.com/">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo-footer.svg" alt="Logo" width="200"
                     height="46">
            </a>
        </div>
        <div class="social-links">
            <a target="_blank" href="#">
                <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M14.785 2.02973C13.7061 1.52495 12.5526 1.15811 11.3465 0.949219C11.1984 1.217 11.0254 1.57718 10.9061 1.8637C9.62404 1.6709 8.35379 1.6709 7.09533 1.8637C6.97605 1.57718 6.79908 1.217 6.64964 0.949219C5.44231 1.15811 4.28741 1.5263 3.20855 2.0324C1.03247 5.32074 0.442574 8.5274 0.737524 11.6885C2.18081 12.7663 3.57953 13.4211 4.95465 13.8495C5.29417 13.3822 5.59698 12.8855 5.85784 12.362C5.36102 12.1732 4.88517 11.9402 4.43555 11.6698C4.55483 11.5814 4.67151 11.489 4.78423 11.394C7.52661 12.6766 10.5063 12.6766 13.2159 11.394C13.3299 11.489 13.4466 11.5814 13.5646 11.6698C13.1136 11.9416 12.6365 12.1745 12.1396 12.3633C12.4005 12.8855 12.702 13.3836 13.0428 13.8508C14.4193 13.4224 15.8193 12.7677 17.2626 11.6885C17.6087 8.02397 16.6714 4.84676 14.785 2.02973ZM6.23145 9.74446C5.40822 9.74446 4.7331 8.97592 4.7331 8.04003C4.7331 7.10414 5.39381 6.33428 6.23145 6.33428C7.06913 6.33428 7.74422 7.1028 7.7298 8.04003C7.73111 8.97592 7.06913 9.74446 6.23145 9.74446ZM11.7687 9.74446C10.9454 9.74446 10.2703 8.97592 10.2703 8.04003C10.2703 7.10414 10.931 6.33428 11.7687 6.33428C12.6063 6.33428 13.2814 7.1028 13.267 8.04003C13.267 8.97592 12.6063 9.74446 11.7687 9.74446Z"
                            fill="white"/>
                </svg>
            </a>
            <a target="_blank" href="#">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M5.54869 0.72495C4.66201 0.766784 4.05651 0.908285 3.52717 1.11629C2.97933 1.32979 2.51499 1.61629 2.05299 2.07996C1.59098 2.54363 1.30648 3.00831 1.09448 3.55698C0.889308 4.08748 0.750307 4.69349 0.71114 5.58067C0.671973 6.46784 0.663306 6.75301 0.667639 9.01603C0.671973 11.2791 0.681973 11.5627 0.724973 12.4517C0.767307 13.3382 0.908309 13.9436 1.11631 14.4731C1.33015 15.0209 1.61632 15.4851 2.08015 15.9473C2.54399 16.4094 3.00833 16.6933 3.55834 16.9056C4.08834 17.1104 4.69451 17.2501 5.58152 17.289C6.46853 17.3278 6.75403 17.3368 9.01639 17.3325C11.2787 17.3281 11.5636 17.3181 12.4524 17.276C13.3413 17.2338 13.9434 17.0918 14.4731 16.8848C15.0209 16.6704 15.4855 16.3848 15.9473 15.9208C16.4091 15.4568 16.6935 14.9918 16.9053 14.4428C17.1106 13.9127 17.2501 13.3066 17.2886 12.4202C17.3275 11.5307 17.3366 11.2467 17.3323 8.98403C17.328 6.72134 17.3178 6.43767 17.2756 5.549C17.2335 4.66032 17.0923 4.05682 16.8845 3.52698C16.6703 2.97914 16.3845 2.5153 15.9208 2.0528C15.4571 1.59029 14.9918 1.30612 14.4429 1.09479C13.9126 0.889618 13.3068 0.749783 12.4198 0.71145C11.5327 0.673116 11.2472 0.663283 8.98406 0.667616C6.72087 0.671949 6.43753 0.681616 5.54869 0.72495ZM5.64602 15.7898C4.83351 15.7544 4.39234 15.6194 4.09834 15.5064C3.709 15.3564 3.43167 15.1751 3.13866 14.8849C2.84566 14.5948 2.66566 14.3164 2.51366 13.9279C2.39949 13.6339 2.26199 13.1932 2.22399 12.3807C2.18265 11.5026 2.17399 11.2389 2.16915 9.01403C2.16432 6.78918 2.17282 6.52584 2.21132 5.64733C2.24599 4.83549 2.38182 4.39382 2.49466 4.09998C2.64466 3.71015 2.82533 3.43331 3.11616 3.14047C3.407 2.84764 3.6845 2.6673 4.07334 2.5153C4.36701 2.40063 4.80768 2.2643 5.61986 2.22563C6.4987 2.18396 6.76203 2.17563 8.98656 2.1708C11.2111 2.16596 11.4751 2.1743 12.3543 2.21296C13.1661 2.2483 13.6079 2.3828 13.9014 2.4963C14.2909 2.6463 14.5681 2.82647 14.8609 3.11781C15.1538 3.40914 15.3343 3.68565 15.4863 4.07532C15.6011 4.36815 15.7375 4.80866 15.7758 5.62133C15.8176 6.50018 15.8271 6.76368 15.8311 8.98803C15.8351 11.2124 15.8273 11.4766 15.7888 12.3547C15.7533 13.1672 15.6186 13.6086 15.5055 13.9029C15.3555 14.2921 15.1746 14.5696 14.8836 14.8623C14.5926 15.1549 14.3154 15.3353 13.9264 15.4873C13.6331 15.6018 13.1919 15.7384 12.3804 15.7771C11.5016 15.8184 11.2382 15.8271 9.01289 15.8319C6.78753 15.8368 6.52503 15.8278 5.64619 15.7898M12.4396 4.54616C12.4399 4.74395 12.4989 4.93721 12.6091 5.10148C12.7192 5.26576 12.8757 5.39367 13.0585 5.46904C13.2414 5.54442 13.4425 5.56387 13.6364 5.52493C13.8304 5.48599 14.0084 5.39042 14.148 5.2503C14.2876 5.11018 14.3825 4.93181 14.4208 4.73774C14.459 4.54367 14.4388 4.34263 14.3628 4.16003C14.2867 3.97743 14.1582 3.82149 13.9935 3.71192C13.8289 3.60235 13.6354 3.54408 13.4376 3.54448C13.1724 3.54501 12.9183 3.65083 12.7312 3.83867C12.544 4.02651 12.4391 4.28099 12.4396 4.54616ZM4.72118 9.00837C4.72585 11.3717 6.6452 13.2832 9.00806 13.2787C11.3709 13.2742 13.2838 11.3551 13.2793 8.9917C13.2748 6.62834 11.3549 4.71632 8.99172 4.72099C6.62853 4.72566 4.71668 6.64534 4.72118 9.00837ZM6.2222 9.00537C6.22111 8.45596 6.38296 7.91857 6.6873 7.46115C6.99163 7.00373 7.42476 6.64683 7.93193 6.43557C8.4391 6.22432 8.99753 6.1682 9.53659 6.27432C10.0757 6.38044 10.5711 6.64402 10.9604 7.03174C11.3497 7.41946 11.6152 7.91391 11.7235 8.45254C11.8317 8.99118 11.7778 9.54983 11.5686 10.0578C11.3593 10.5658 11.0041 11.0004 10.5479 11.3065C10.0917 11.6127 9.55496 11.7766 9.00556 11.7777C8.64075 11.7785 8.27937 11.7074 7.94204 11.5685C7.60472 11.4296 7.29807 11.2255 7.0396 10.9681C6.78113 10.7107 6.57591 10.4048 6.43566 10.068C6.29541 9.73127 6.22287 9.37017 6.2222 9.00537Z"
                            fill="white"/>
                </svg>
            </a>
            <a target="_blank" href="#">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M17.333 9.03045C17.333 4.4113 13.602 0.666668 8.99979 0.666668C4.39755 0.666668 0.666626 4.4113 0.666626 9.03045C0.666626 12.9527 3.35724 16.244 6.98683 17.148V11.5864H5.26853V9.03045H6.98683V7.9291C6.98683 5.08241 8.27047 3.76294 11.0551 3.76294C11.5831 3.76294 12.4941 3.86698 12.8667 3.97069V6.28746C12.6701 6.26672 12.3284 6.25635 11.9041 6.25635C10.5378 6.25635 10.0098 6.77591 10.0098 8.12649V9.03045H12.7317L12.2641 11.5864H10.0098V17.333C14.136 16.8328 17.3333 13.3067 17.3333 9.03045H17.333Z"
                            fill="white"/>
                </svg>
            </a>
            <a target="_blank" href="#">
                <svg width="17" height="18" viewBox="0 0 17 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M10.1888 7.72386L16.1452 0.666668H14.7337L9.56179 6.79433L5.431 0.666668H0.666626L6.91319 9.93278L0.666626 17.3333H2.07817L7.53984 10.8623L11.9023 17.3333H16.6666L10.1885 7.72386H10.1888ZM8.25549 10.0144L7.62259 9.09172L2.58677 1.74973H4.75483L8.81879 7.67495L9.45169 8.59765L14.7344 16.2995H12.5663L8.25549 10.0148V10.0144Z"
                            fill="white"/>
                </svg>
            </a>
        </div>
        <p class="copyright-text">© 2024 Megatrader</p>
    </div>
</div>

<!-- MT Error Modal -->
<div id="mt-error-modal" class="modal modal-subcription fade" tabindex="-1" aria-labelledby="mt-error-title"
     aria-hidden="true" hidden>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content gap-32">
            <div class="modal-header w-100 border-0 justify-content-between align-items-center p-0">
                <span id="mt-error-title" class="modal-title text-white heading-sm-medium">ERROR</span>
                <button type="button" class="p-0 border-0 bg-transparent shadow-none mt-modal__close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
          <span aria-hidden="true">
            <img src="/wp-content/uploads/2025/05/cancel-circle-1.png" alt="Close" style="width:24px;height:24px;">
          </span>
                </button>
            </div>

            <div class="modal-body d-flex flex-column align-items-center text-center gap-2">
                <div aria-hidden="true">
                    <div class="modal-body-image modal-image-warning">
                        <img id="mt-error-icon" decoding="async"
                             src="/wp-content/themes/megatrader-addons/assets/img/error.svg" alt="Warning icon">
                    </div>
                </div>
                <span id="mt-error-headline"
                      class="fw-medium leading-60px text-5xl text-uppercase text-white mt-2">Ups!</span>
                <span id="mt-error-message" class="fw-medium text-a8a29e text-base">
          Ocurrió un error inesperado.
        </span>

                <button type="button" class="mega-btn-md mega-btn-primary-md mt-4" data-bs-dismiss="modal"
                        aria-label="Close">Close
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-mega">
        <div class="modal-content">
            <div class="modal-header w-100 border-0 justify-content-between align-items-start p-0">
                <h5 class="modal-title text-white heading-sm-medium text-uppercase" id="termsModalLabel">Terms of
                    Service</h5>
                <button type="button" class="p-0 border-0 bg-transparent shadow-none" data-bs-dismiss="modal"
                        aria-label="Close">
          <span aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                 data-slot="icon" class="text-white w-6 h-6">
              <path fill-rule="evenodd"
                    d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z"
                    clip-rule="evenodd"></path>
            </svg>
          </span>
                </button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center w-100 my-35 px-0">
                <div class="pe-3">
                    <h3 class="text-uppercase text-white leading-7 text-2xl fw-medium">MegaTrader Terms of Service</h3>
                    <p>By using our services, you agree to comply with and be bound by the following Terms of Service.
                        Please
                        review the following terms carefully. If you do not agree to these terms, you should not use
                        this site or
                        our services.</p>
                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Overview of MegaTrader
                        Services </h3>
                    <p class="leading-normal text-base fw-medium">MegaTrader is a
                        simulation-based trading platform that allows individuals to engage in realistic market-like
                        trading
                        experiences without the risks associated with actual capital. The platform offers a robust
                        environment
                        for users to test their trading strategies, improve discipline, and undergo structured
                        evaluations.
                        Using proprietary algorithms and internally generated market conditions, MegaTrader emulates
                        real-time
                        order behavior, trade execution, and account performance within a fully closed system.</p>
                    <p class="leading-normal text-base fw-medium">Importantly,
                        MegaTrader does not facilitate live market trading or act as a broker-dealer. All activity
                        occurs within
                        the simulated ecosystem and serves to assess consistency, behavior, and adherence to risk
                        parameters.
                        Our primary goal is to help traders develop sustainable habits and decision-making processes,
                        while also
                        offering an opportunity to qualify for performance-based incentives. The system mimics key
                        market
                        dynamics but remains separate from any financial exchanges or custodial institutions. We make it
                        clear
                        that all evaluations occur in a controlled, educational environment without exposure to real
                        capital or
                        financial market risks.</p>
                    <p class="leading-normal text-base fw-medium">MegaTrader is
                        designed to reward skill, patience, and discipline. While it is not a substitute for financial
                        certification or live experience, it provides a practical and structured way to enhance trading
                        acumen
                        through virtual account usage, performance tracking, and merit-based advancement
                        opportunities.</p>
                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Eligibility and User
                        Requirements
                    </h3>
                    <p class="leading-normal text-base fw-medium">Access to MegaTrader is restricted to individuals who
                        meet
                        specific age, location, and identity criteria. To use the platform, you must:
                    </p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Be at
                            least 18
                            years old or the legal age of majority in your jurisdiction
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Provide accurate
                            and up-to-date registration details, including full name, email address, and billing
                            information
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Reside in a
                            country that is not listed on our restricted jurisdictions list
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">We reserve the right to validate your eligibility at
                        any
                        point during your use of the platform. This may involve identity verification (KYC), geolocation
                        checks, or
                        other documentation requests. Users found to have submitted falsified data or misrepresented
                        their identity
                        are subject to immediate suspension or permanent termination without refund.
                    </p>
                    <p class="leading-normal text-base fw-medium">Furthermore, participation in MegaTrader programs may
                        require
                        legal capacity to enter into binding agreements, and users must agree to abide by all platform
                        rules,
                        including evaluation conditions and community conduct standards. Corporate or institutional
                        access may be
                        subject to separate agreements or restrictions. By registering, you affirm that you meet all
                        requirements
                        and understand that eligibility is continuously reviewed for compliance purposes.
                    </p>
                    <p class="leading-normal text-base fw-medium">Continued access to MegaTrader is not guaranteed and
                        may be
                        withdrawn at our discretion if a user becomes legally ineligible or if platform policies are
                        updated to
                        reflect evolving compliance or operational standards.
                    </p>
                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Account Creation and
                        Management</h3>
                    <p class="leading-normal text-base fw-medium">Creating an account with MegaTrader is the first step
                        toward
                        participating in our simulated trading programs. To register, users must provide a valid email
                        address,
                        choose a secure password, and agree to the platform’s Terms and Privacy Policy. Upon completion
                        of the
                        registration process, users receive access to a dashboard where they can configure their
                        profile, select a
                        simulation plan, and begin trading with a virtual account.
                    </p>
                    <p class="leading-normal text-base fw-medium">Maintaining the security of your account is your sole
                        responsibility. This includes:
                    </p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Keeping your
                            login credentials confidential and unique
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Enabling
                            two-factor authentication (2FA) if available
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Monitoring your
                            account for unusual activity or unauthorized access attempts
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Promptly updating
                            your contact and verification information as needed
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">Account sharing is strictly prohibited. Each account
                        is intended
                        for use by a single individual, and any attempts to allow others to trade under your profile may
                        result in
                        suspension. Similarly, use of false identity, impersonation, or opening multiple accounts
                        without approval
                        may be treated as a breach of platform policy.
                    </p>
                    <p class="leading-normal text-base fw-medium">If you believe your account has been compromised, you
                        must
                        notify our support team immediately. MegaTrader may temporarily suspend access while
                        investigating and
                        verifying user ownership. We reserve the right to disable or close accounts that present
                        security threats or
                        violate our conduct rules, with or without advance warning, to protect the integrity of the
                        platform.
                    </p>
                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">User Responsibilities and
                        Conduct
                    </h3>
                    <p class="leading-normal text-base fw-medium">As a user of MegaTrader, you are expected to uphold
                        the
                        principles of integrity, fairness, and respect for the simulated environment we provide. This
                        includes
                        adhering to our trading rules, evaluation standards, and platform conduct guidelines. Your
                        responsibilities
                        extend beyond merely using the interface—they involve contributing to a community built on
                        discipline,
                        accountability, and ethical behavior.
                    </p>
                    <p class="leading-normal text-base fw-medium">Key user obligations include: </p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Engaging with the
                            platform only for personal, non-commercial simulation purposes

                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Following
                            evaluation guidelines, trading risk parameters, and account progression requirements
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Refraining from
                            disruptive behavior such as exploiting bugs, using automation tools, or submitting
                            misleading information
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Respecting other
                            users and staff in all forms of communication, including support inquiries and community
                            forums
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">You are also responsible for reviewing all platform
                        updates and
                        policy changes as posted on our website or within your dashboard. Violations of platform
                        integrity—including
                        impersonation, multi-account abuse, or falsified data—will result in immediate account
                        restriction or
                        permanent ban. We reserve the right to monitor activity to protect against manipulation or
                        misuse.
                    </p>
                    <p class="leading-normal text-base fw-medium">By continuing to use MegaTrader, you confirm your
                        understanding
                        that the environment is designed for skill-building, and that any breach of this purpose may
                        lead to
                        administrative or legal consequences.</p>
                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Financial Disclaimer and
                        Advisory
                        Limitations</h3>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader is not a financial advisory service and does not provide any investment advice,
                        recommendations,
                        or regulated financial services. The platform operates solely as a simulated environment for
                        educational and
                        skill-development purposes. Users should not interpret any material—whether written, visual, or
                        algorithmic—as a suggestion to buy, sell, or hold any financial instrument or security.
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        All content available on MegaTrader, including articles, platform analytics, or performance
                        statistics, is
                        designed to support trading practice, not financial decision-making. We do not:
                    </p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Tailor strategies
                            to individual risk tolerance or investment goals
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Offer
                            advice
                            about securities, derivatives, or real-world trading accounts
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Guarantee the
                            performance or profitability of any simulation outcome
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        You are solely responsible for your use of the information provided and should consult a
                        licensed financial
                        advisor before engaging in real capital trading. By participating in our platform, you
                        acknowledge that no
                        fiduciary relationship exists and that all content is provided “as-is” without warranties or
                        guarantees of
                        financial accuracy or suitability.
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader disclaims any liability related to user actions or decisions based on simulations or
                        insights
                        gained through the platform. The educational framework provided is not a substitute for
                        professional
                        financial planning, portfolio analysis, or legal guidance.
                    </p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Payments, Refunds, and
                        Fees</h3>
                    <p class="leading-normal text-base fw-medium">
                        All users of MegaTrader agree to pay applicable fees for participation in simulations,
                        challenges, or other
                        optional services offered on the platform. Payment is processed securely through authorized
                        vendors or
                        crypto payment gateways. All fees are denominated in U.S. dollars unless stated otherwise, and
                        full payment
                        must be received before account activation or access to paid features is granted.
                    </p>
                    <p class="leading-normal text-base fw-medium">Please note:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">All
                            purchases are
                            final and non-refundable unless otherwise stated in writing
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">No
                            refunds are
                            issued for failing to meet challenge criteria or evaluation benchmarks
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Chargebacks or
                            payment disputes may result in immediate account termination
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        Users must ensure that payment methods used are authorized and do not originate from restricted
                        jurisdictions. We may store transaction logs for internal security reviews and compliance with
                        anti-money
                        laundering (AML) policies. If a pricing or billing error is identified, MegaTrader reserves the
                        right to
                        adjust the charge or correct access privileges accordingly.
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        We also reserve the right to revise pricing structures at any time. This may include changes to
                        challenge
                        entry fees, evaluation tier pricing, and optional add-on services. Notice of pricing changes
                        will be
                        communicated through your dashboard, email, or the platform’s billing section. Continued use of
                        the service
                        after price adjustments constitutes acceptance of the new fee structure.
                    </p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Payouts and Eligibility
                        Criteria</h3>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader offers real monetary payouts to users who successfully complete platform-specific
                        trading
                        evaluations and meet all eligibility requirements. These payouts serve as performance-based
                        rewards for
                        traders who demonstrate consistency, discipline, and adherence to trading rules.
                    </p>
                    <p class="leading-normal text-base fw-medium">To qualify, users must:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Pass
                            all
                            evaluation criteria and minimum trading day requirements
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Maintain risk
                            parameters without exceeding daily or trailing drawdowns
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Adhere strictly
                            to position sizing rules and avoid disqualifying behavior
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        Once qualified, users may request a payout through their dashboard, which will be processed via
                        supported
                        channels such as cryptocurrency or partner withdrawal services. Verification
                        procedures—including identity
                        checks, IP location reviews, and performance audits—must be completed prior to payout release.
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        Payouts are not guarantees of future performance nor an investment product. They are conditional
                        incentives
                        based on simulated trading metrics. MegaTrader does not promise income, employment, or recurring
                        disbursements. Delays may occur due to user errors, suspicious activity, or compliance holds.
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        All decisions regarding payout eligibility, amount, and disbursement method are final and
                        subject to
                        internal review. Any abuse of payout mechanics may result in forfeiture and potential account
                        suspension.
                    </p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Data Collection and
                        Usage</h3>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader is committed to safeguarding the personal and behavioral data collected during your
                        use of the
                        platform. Your data is processed in accordance with our Privacy Policy, which outlines how we
                        gather, store,
                        and use information to maintain platform integrity, enable security controls, and support
                        feature
                        functionality.
                    </p>
                    <p class="leading-normal text-base fw-medium">Types of data collected include:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Personally
                            identifiable information such as your name, email, IP address, and government-issued ID (if
                            required for
                            KYC)
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Trading metrics
                            like order timing, strategy preferences, win/loss ratios, and behavioral patterns
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Device, browser,
                            and location data to help monitor system compatibility and detect fraudulent access
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        Data is used for authentication, support response, challenge administration, and performance
                        analytics. We
                        may also use de-identified aggregates to improve system performance, evaluate platform features,
                        and test
                        new tools.
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader shares your information only with trusted service providers who are contractually
                        obligated to
                        maintain data confidentiality and comply with data security standards.
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        We retain your data only as long as necessary to fulfill its purpose or comply with legal
                        requirements. You
                        may request deletion or data access in accordance with applicable privacy laws by contacting us
                        at
                        <a href="mailto:support@megatrader.io" class="text-ffb54d">support@megatrader.io</a>. Please
                        allow up to 30
                        days for a response, and note that certain requests may be
                        limited based on legal obligations or risk controls.
                    </p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Suspension and Termination
                        Policy
                    </h3>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader reserves the right to suspend, restrict, or terminate your access to the platform if
                        you violate
                        these Terms of Service, attempt to manipulate the simulation, or otherwise compromise platform
                        fairness and
                        security. We take violations seriously and employ internal monitoring tools to identify
                        suspicious behavior
                        across accounts and sessions.
                    </p>
                    <p class="leading-normal text-base fw-medium">Examples of conduct that may trigger account action
                        include:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Submitting
                            falsified personal or payment information
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Using
                            bots,
                            scripts, or automation to perform trades
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Exploiting bugs,
                            fill behavior, or other unintended platform mechanics
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Accessing the
                            service from a prohibited or misrepresented jurisdiction
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        In some cases, users may receive a warning or temporary suspension. In others—particularly
                        involving fraud,
                        multiple offenses, or system abuse—permanent bans will be issued without refund. Account reviews
                        are
                        performed at our sole discretion and may require documentation or activity logs to resolve.
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader is not responsible for any data loss or missed opportunities caused by suspension or
                        termination.
                        You waive all rights to contest enforcement actions outside the scope of the appeal channels
                        outlined in our
                        support procedures.
                    </p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Governing Law and Legal
                        Jurisdiction
                    </h3>
                    <p class="leading-normal text-base fw-medium">
                        These Terms are governed and interpreted in accordance with the laws of the State of Florida,
                        United States
                        of America, without regard to conflict-of-law provisions. By using MegaTrader, you agree to
                        resolve any
                        disputes through legally binding arbitration under the rules of a mutually agreed-upon
                        arbitration body
                        located in Miami-Dade County, Florida.
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        By accepting these Terms, you also:
                    </p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Waive
                            your right
                            to bring or participate in any class action, mass claim, or representative lawsuit
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Agree
                            that all
                            legal claims shall be brought in an individual capacity only
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Consent to the
                            exclusive venue and jurisdiction of Miami-Dade County courts for any claims not resolved
                            through
                            arbitration
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        These legal terms are designed to streamline dispute resolution and prevent unnecessary or
                        costly
                        litigation. If any part of these Terms is deemed unenforceable, the remaining provisions shall
                        continue in
                        full force and effect.
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        You further acknowledge that MegaTrader’s services are subject to applicable U.S. laws, and that
                        your use of
                        the platform may be restricted or subject to disclosure obligations under international
                        regulatory
                        agreements or enforcement requests.
                    </p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Jurisdictional
                        Restrictions</h3>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader enforces strict jurisdictional restrictions based on U.S. export laws, economic
                        sanctions, and
                        internal compliance policies. These restrictions help ensure lawful platform operation and
                        reduce the risk
                        of engagement with high-risk or embargoed regions. Users from the following countries are
                        prohibited from
                        accessing MegaTrader:
                    </p>
                    <p class="leading-normal fw-bold text-stone-300">
                        Afghanistan, Central African Republic, Congo (Brazzaville), Congo (Kinshasa), Cuba,
                        Guinea-Bissau, Iran,
                        Iraq, North Korea, Libya, Mali, Russia, Somalia, South Sudan, Sudan, Syria, Yemen, Venezuela
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        These restrictions apply regardless of whether the user attempts access via proxy, VPN, or
                        third-party
                        routing. If you are physically located in or primarily reside in one of these jurisdictions, you
                        may not use
                        MegaTrader, register an account, or receive any platform services.
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader actively monitors for signs of IP obfuscation, proxy masking, and geographic
                        anomalies in login
                        activity. Users found circumventing these controls will be permanently suspended, and any
                        benefits earned in
                        violation of this policy may be forfeited.
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        It is your responsibility to understand the laws applicable in your country before using the
                        platform. We
                        will not be liable for access interruptions or bans resulting from jurisdictional compliance
                        enforcement.
                    </p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Updates to the Terms of
                        Service</h3>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader reserves the right to update, revise, or replace these Terms of Service at any time,
                        with or
                        without direct user notification. Changes may be made to reflect updated legal requirements,
                        modifications
                        to the platform’s functionality, or shifts in company policy.
                    </p>
                    <p class="leading-normal text-base fw-medium">When updates occur:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">The
                            "Last
                            Updated" date at the top of the Terms will reflect the most recent revision
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Significant or
                            material changes may be announced via email, banner notifications, or account messaging
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Your
                            continued
                            use of the platform after such changes indicates acceptance of the revised Terms
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        We recommend that all users review these Terms periodically, especially when prompted by system
                        messages or
                        notices. In some cases, updated Terms may require you to reaffirm your agreement before
                        accessing certain
                        services.
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        If you do not agree to the new Terms, you must cease using the platform and may request account
                        closure.
                        MegaTrader is not responsible for failure to review or understand revised terms. Staying
                        informed about your
                        rights and responsibilities ensures a smooth and secure experience.
                    </p>
                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Contact Information</h3>
                    <p class="leading-normal text-base fw-medium">
                        If you have any questions, feedback, or need assistance regarding the Terms of Service or any
                        other aspect
                        of the MegaTrader platform, our support team is available to help. We offer multiple contact
                        channels for
                        fast, secure, and user-friendly communication.
                    </p>
                    <p class="leading-normal text-base fw-medium">Contact methods include:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Support Chat:</span> You
                            can open a support ticket directly through the live chat widget on the MegaTrader website or
                            platform
                            dashboard. This is the fastest way to receive responses to technical or account-related
                            issues.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Email Support:</span>
                            For detailed questions, documentation issues, or compliance concerns, email us at <a
                                    href="mailto:support@megatrader.io" class="text-ffb54d">support@megatrader.io</a>.
                            Include your registered email address and a clear subject line to ensure faster processing.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        Our support team generally responds within 1-3 business days depending on ticket volume and the
                        complexity
                        of your inquiry. Please avoid submitting multiple duplicate requests, as this may delay
                        processing.
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        We are committed to providing clear and professional assistance while respecting your privacy
                        and ensuring
                        timely communication. For sensitive or legal matters, please clearly indicate the nature of the
                        request and
                        provide any required verification materials upon request.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-mega">
        <div class="modal-content">
            <div class="modal-header w-100 border-0 justify-content-between align-items-start p-0">
                <h5 class="modal-title text-white heading-sm-medium text-uppercase" id="privacyModalLabel">Privacy
                    Policy</h5>
                <button type="button" class="p-0 border-0 bg-transparent shadow-none" data-bs-dismiss="modal"
                        aria-label="Close">
          <span aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                 data-slot="icon" class="text-white w-6 h-6">
              <path fill-rule="evenodd"
                    d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z"
                    clip-rule="evenodd"></path>
            </svg>
          </span>
                </button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center w-100 my-35 px-0">
                <div class="pe-3">
                    <h3 class="text-uppercase text-white leading-7 text-2xl fw-medium">MegaTrader Privacy Policy</h3>
                    <p class="leading-normal text-base fw-medium">
                        This Privacy Policy explains how MegaTrader collects, uses, stores, and protects your personal
                        data when you
                        access our website, use our services, or engage with our platform in any way. We are committed
                        to protecting
                        your privacy and handling your data in compliance with applicable data protection laws.
                    </p>
                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Information We Collect</h3>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader collects various types of personal and technical information to operate the platform
                        effectively
                        and ensure compliance with applicable regulations. This includes information provided directly
                        by users,
                        data collected automatically through your interaction with the platform, and limited data from
                        third-party
                        integrations.
                    </p>
                    <p class="leading-normal text-base fw-medium">Collection practices are designed to balance
                        operational needs
                        with privacy and user transparency.</p>
                    <p class="leading-normal text-base fw-medium">We may collect:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Identity Information:</span>Your full name, email
                            address, country of
                            residence, and username or password credentials.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Technical Information:</span>IP addresses, device
                            identifiers, browser
                            type, operating system, time zone settings, language preferences, and login timestamps.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Usage Data:</span>Simulated trades, session length,
                            page views,
                            clickstream data, account performance, behavioral trends, and interaction history.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Communications:</span>Chat transcripts, emails, and
                            support ticket
                            detailsb that help us understand and resolve platform issues.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        In addition, we may use automated tools such as cookies, pixels, and analytics trackers to
                        gather behavioral
                        insights and enhance user experience. By using MegaTrader, you consent to the collection of this
                        information
                        in accordance with this Privacy Policy and applicable laws.
                    </p>
                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Use of Collected Data</h3>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader uses your data to provide platform functionality, enhance performance, ensure user
                        security, and
                        support compliance efforts. All data is processed under a legitimate operational or legal basis,
                        and we make
                        every effort to minimize data collection to only what is strictly necessary for service
                        delivery.
                    </p>
                    <p class="leading-normal text-base fw-medium">We may use your data to:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Authenticate and Manage Accounts</span>: Ensuring
                            secure login access,
                            verifying identities, and enabling account recovery.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Deliver Platform Features</span>: Running
                            simulations, storing trading
                            metrics, awarding rewards, and presenting personalized content.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Enable Communication</span>: Sending service updates,
                            responding to
                            support
                            requests, or delivering reminders, offers, and notifications.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Monitor and Improve Services</span>: Tracking
                            performance bottlenecks,
                            crash data, platform usability, and user engagement analytics.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Ensure Compliance and Detect Abuse</span>:
                            Investigating suspicious
                            activity, enforcing terms of service, preventing fraud, and responding to legal inquiries.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        All data usage is restricted to internal operations and approved third-party processors under
                        contract. You
                        can modify preferences and withdraw consent for non-essential data usage at any time via your
                        dashboard
                        settings.
                    </p>
                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Data Sharing With Third
                        Parties</h3>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader does not sell, rent, or commercially trade your personal information. We only share
                        your data
                        with trusted third-party vendors who help us operate the platform and meet legal or technical
                        obligations.
                        All
                        sharing is conducted under confidentiality agreements and aligns with applicable data protection
                        laws like
                        GDPR and CCPA.
                    </p>
                    <p class="leading-normal text-base fw-medium">We may share your data with:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Payment Processors:</span>To handle transactions,
                            prevent fraud, and
                            verify purchases (e.g. Stripe, crypto payment gateways).
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Analytics and Tracking Providers:</span> Services
                            like Google Analytics
                            help us understand platform performance and user behavior.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Cloud Infrastructure and Storage Services:</span>Used
                            for hosting,
                            backups, and database operations (e.g., AWS, Firebase).
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Security and Fraud Monitoring Tools:</span>To detect
                            abuse, account
                            sharing, bot activity, or other violations.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Legal and Regulatory Authorities:</span>When required
                            by law, subpoena,
                            or government order to comply with regulatory obligations.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        We never authorize third-party partners to reuse or repurpose your data for unrelated commercial
                        use. All
                        vendors must adhere to strict security and privacy standards as outlined in their Data
                        Processing Agreements
                        with MegaTrader.
                    </p>
                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Data Storage and
                        Protection</h3>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader uses advanced digital security frameworks and operational protocols to protect your
                        data from
                        unauthorized access, loss, or misuse. We treat your personal information with the highest level
                        of
                        confidentiality and implement multiple layers of protection, including physical, technical, and
                        administrative safeguards.
                    </p>
                    <p class="leading-normal text-base fw-medium">Our security practices include:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Encryption Protocols:</span>All user data is
                            encrypted both in transit
                            and at rest using industry-standard TLS and AES-256.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Access Control:</span>Role-based access systems
                            ensure that only
                            authorized personnel can view or interact with sensitive data.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Secure Infrastructure:</span>We host data on secured
                            cloud services with
                            redundancy, disaster recovery, and uptime guarantees.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Continuous Monitoring:</span>Our systems are
                            monitored for anomalies,
                            unauthorized access attempts, and potential breaches in real time.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Penetration Testing: </span>We conduct regular
                            security audits and
                            third-party assessments to identify and mitigate vulnerabilities.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        In addition to institutional safeguards, we encourage users to:
                    </p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Enable two-factor
                            authentication (2FA) to further protect their accounts
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Use
                            strong,
                            unique passwords and avoid credential reuse
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Log
                            out of their
                            accounts when using public devices
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        If you detect suspicious activity, please notify us immediately at <a
                                href="mailto:support@megatrader.io"
                                class="text-ffb54d">support@megatrader.io</a> so we can take corrective action.
                    </p>
                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">User Control and Data
                        Rights</h3>
                    <p class="leading-normal text-base fw-medium">MegaTrader empowers users to control how their data is
                        collected, stored, and used. You have full rights to access, correct, limit, or delete your
                        personal data at
                        any time, in accordance with international privacy regulations like the GDPR and CCPA. Our
                        platform and
                        support systems are built with data transparency and accessibility in mind.</p>
                    <p class="leading-normal text-base fw-medium">You may:</p>

                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">View Stored Data:</span>Review your personal and
                            activity-related
                            information via your account dashboard.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Update or Correct Information:</span>Change your
                            profile details, update
                            contact preferences, or fix inaccuracies.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Limit Processing:</span>Restrict the use of your data
                            for non-essential
                            communications, cookies, or analytics.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Withdraw Consent:</span>Opt out of promotional emails
                            or disable tracking
                            features through privacy settings.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Request Deletion:</span>Submit an account deletion
                            request to have your
                            data permanently removed from our systems.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">To exercise these rights, submit a request
                        through:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Live Chat</span>on our platform, or
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Email:</span><a href="mailto:support@megatrader.io"
                                                                                   class="text-ffb54d">support@megatrader.io</a>
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">We will respond within a reasonable time frame,
                        typically 2–5
                        business days, and fulfill validated deletion or access requests unless restricted by compliance
                        requirements or legal holds.
                    </p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Cookies and Tracking
                        Technologies
                    </h3>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader uses cookies and tracking technologies to enhance the functionality, security, and
                        personalization of the platform. These tools allow us to recognize returning users, streamline
                        login
                        sessions, improve page load performance, and detect technical issues or malicious behavior. We
                        are committed
                        to maintaining transparency and giving you control over cookie settings.
                    </p>
                    <p class="leading-normal text-base fw-medium">Types of cookies we use:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Essential Cookies:</span>Required for core site
                            functions like
                            authentication, fraud detection, and session persistence.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Performance Cookies:</span>Collect data on how users
                            interact with the
                            platform to optimize layout, speed, and accessibility.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Functional Cookies: </span>Store language
                            preferences, user settings, and
                            UI personalization details.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Marketing Cookies (opt-in only):</span>Help us
                            deliver relevant ads and
                            measure the effectiveness of promotional efforts across channels.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        Cookie management options:
                    </p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">You
                            can modify
                            cookie preferences at any time through our in-app cookie banner.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Most
                            browsers
                            allow you to disable cookies, though this may affect functionality.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">You
                            can clear
                            cookies stored on your device through browser settings or private browsing modes.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        For more information about cookies used on MegaTrader and your opt-out choices, visit our
                        Cookies Policy
                        page or contact <a href="mailto:support@megatrader.io"
                                           class="text-ffb54d">support@megatrader.io</a>.
                    </p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Handling of Children’s
                        Data</h3>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader’s platform is not designed or intended for individuals under the age of 18. We do not
                        knowingly
                        collect, process, or retain any personal data from minors, and we take strict measures to
                        prevent underage
                        access to our services.
                    </p>
                    <p class="leading-normal text-base fw-medium">Our policy includes:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Age Verification:</span> We may implement manual or
                            automated tools to
                            verify that users meet the minimum age requirement.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Account Monitoring:</span>If an account is suspected
                            of belonging to a
                            minor, we will take immediate steps to investigate and restrict access.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Prompt Deletion:</span>In cases where underage usage
                            is discovered, we
                            will delete all associated data and suspend the account without delay.
                        </li>
                    </ul>

                    <p class="leading-normal text-base fw-medium">
                        Parents or guardians who believe that their child has used the MegaTrader platform without
                        permission should
                        contact us immediately at <a href="mailto:support@megatrader.io"
                                                     class="text-ffb54d">support@megatrader.io</a>. Upon verification of
                        such claims, we will take all
                        necessary actions to secure and erase the child's data.
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        We remain committed to compliance with child protection laws, including the Children’s Online
                        Privacy
                        Protection Act (COPPA) in the United States and similar global regulations. By using the
                        platform, you
                        affirm that you are 18 years of age or older and legally able to enter into binding agreements.
                    </p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Data Retention
                        Practices</h3>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader retains personal and technical data only as long as necessary to fulfill operational,
                        legal, and
                        regulatory obligations. Our retention schedules are designed to balance performance
                        optimization, audit
                        requirements, and your privacy rights.
                    </p>
                    <p class="leading-normal text-base fw-medium">Data retention timelines include:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Active User Accounts:</span>Retained indefinitely
                            while the account
                            remains in good standing and actively used.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Inactive Accounts:</span>May be marked for deletion
                            or anonymization
                            after 12 consecutive months of inactivity.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Support Interactions:</span>Retained for up to 24
                            months to assist in
                            dispute resolution, audits, and training.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Deleted Accounts:</span>Once a deletion request is
                            verified, all
                            associated data is purged from live systems and queued for erasure from backup servers
                            within 30–60 days.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        Certain data may be preserved beyond these periods to:
                    </p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Comply with
                            financial, tax, or legal obligations
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Fulfill
                            contractual audit or investigation requirements
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            Prevent fraud,
                            abuse, or platform manipulation
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        You can request early deletion by contacting <a href="mailto:support@megatrader.io"
                                                                        class="text-ffb54d">support@megatrader.io</a> or
                        through our live chat. Once processed, you will receive
                        confirmation, and no residual data will be stored unless required by law.
                    </p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Policy Updates and
                        Revisions</h3>
                    <p class="leading-normal text-base fw-medium">
                        MegaTrader may update or revise this Privacy Policy from time to time to reflect changes in data
                        handling
                        practices, legal requirements, or business operations. Any updates will be posted prominently on
                        our
                        website, and the "Last Updated" date at the top of the page will reflect the latest revision.
                    </p>
                    <p class="leading-normal text-base fw-medium">Update protocols include:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Material Changes:</span>For significant changes
                            affecting your rights,
                            such as expanded data usage or third-party integrations, we will provide additional notice
                            via email or
                            in-dashboard alerts.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Minor or Technical Edits:</span>Updates that clarify
                            language,
                            restructure content, or revise terminology will be posted without separate notification.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Version Control:</span>Older versions of the policy
                            may be archived and
                            made available upon request for transparency.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        We encourage all users to review this policy periodically. Your continued use of the MegaTrader
                        platform
                        after changes take effect constitutes acceptance of the revised policy. If you do not agree with
                        the
                        changes, you should discontinue use and contact us to manage your data or close your account.
                    </p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Contacting MegaTrader
                        Regarding
                        Privacy</h3>
                    <p class="leading-normal text-base fw-medium">
                        If you have any questions, concerns, or requests related to MegaTrader’s Privacy Policy or how
                        your data is
                        handled, you are encouraged to reach out to our team. We value user feedback and take all
                        inquiries
                        seriously.
                    </p>
                    <p class="leading-normal text-base fw-medium">You may contact us through:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Live Chat:</span>Available directly on the MegaTrader
                            website or user
                            dashboard. You can open a support ticket for privacy-related matters.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Email Support:</span>Send your inquiries to <a
                                    href="mailto:support@megatrader.io" class="text-ffb54d">support@megatrader.io</a>
                            with the subject line
                            "Privacy Request" for faster routing.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium">
                        Whether you’re seeking clarification, requesting access to your data, or submitting a complaint,
                        we are here
                        to assist you. All submissions will be acknowledged promptly and handled within our standard
                        response window
                        of 2–5 business days, depending on the complexity of the request.
                    </p>
                    <p class="leading-normal text-base fw-medium">
                        We strive to respond clearly, respectfully, and with a commitment to protecting your rights and
                        clarifying
                        your data options.
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="disclaimerModal" tabindex="-1" aria-labelledby="disclaimerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-mega">
        <div class="modal-content">
            <div class="modal-header w-100 border-0 justify-content-between align-items-start p-0">
                <h5 class="modal-title text-white heading-sm-medium text-uppercase" id="disclaimerModalLabel">
                    Disclaimer</h5>
                <button type="button" class="p-0 border-0 bg-transparent shadow-none" data-bs-dismiss="modal"
                        aria-label="Close">
          <span aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                 data-slot="icon" class="text-white w-6 h-6">
              <path fill-rule="evenodd"
                    d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z"
                    clip-rule="evenodd"></path>
            </svg>
          </span>
                </button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center w-100 my-35 px-0">
                <div class="pe-3">
                    <h3 class="text-uppercase text-white leading-7 text-2xl fw-medium">MegaTrader Disclosure Policy</h3>
                    <p class="leading-normal text-base fw-medium">This Disclosure Policy outlines important information
                        regarding the nature of MegaTrader’s services, limitations of our simulated trading environment,
                        and disclosures required under applicable laws and internal operating principles. By using
                        MegaTrader, you agree that you have read, understood, and accepted the disclosures outlined
                        below.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Nature of MegaTrader
                        Services</h3>
                    <p class="leading-normal text-base fw-medium">MegaTrader provides a simulated futures trading
                        platform intended solely for educational, skill-development, and entertainment purposes. It is
                        not a live brokerage or trading venue and does not connect to any real financial exchanges or
                        execution venues. Users trade using simulated funds within a closed-loop environment designed to
                        replicate market-like conditions for evaluation purposes.</p>
                    <p class="leading-normal text-base fw-medium">Key service characteristics include:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Simulation-Only Environment</span>: All trading takes
                            place within a simulated environment using virtual capital and market data. No real trades
                            are executed in any live market.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Non-Investment Functionality</span>: MegaTrader does
                            not offer investment services or act as a broker, advisor, or portfolio manager. It does not
                            engage in fiduciary activities.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Educational Orientation</span>: The primary intent is
                            to provide users with a platform where they can build confidence, discipline, and
                            performance habits by simulating real-world market scenarios.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Performance-Based Incentives</span>: While some users
                            may become eligible for performance-based rewards (e.g., payouts), this is not guaranteed
                            and is conditional upon meeting rigorous criteria.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">Participation in MegaTrader should be approached
                        as an educational tool rather than a source of investment return. Any rewards earned within the
                        platform are based solely on simulated outcomes and compliance with platform rules. Users should
                        not expect their account activity to result in ownership of actual securities or positions.</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                            MegaTrader is not a broker-dealer, financial advisor, or investment platform.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">We emphasize that participation in simulations
                        does not equate to real-market exposure, and no real money is deposited into or traded from live
                        markets.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Limitations of Simulated
                        Results</h3>
                    <p class="leading-normal text-base fw-medium">No. While MegaTrader is designed to closely mimic the
                        behavior of real futures markets, results achieved in a simulated environment should never be
                        interpreted as accurate predictions of live trading outcomes. Numerous variables affect
                        real-market trading that are either minimized or excluded in simulations.</p>
                    <p class="leading-normal text-base fw-medium">Differences include:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Emotional Detachment</span>: Simulated trading lacks
                            the psychological pressure that comes with risking real money, which can greatly affect
                            decision-making.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">No Real Execution Costs</span>: Slippage, partial
                            fills, and liquidity limitations are either simplified or absent in the simulation.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Unrealistic Consistency</span>: Users may develop
                            habits or take risks in simulations that they would not attempt in live trading due to fear,
                            uncertainty, or capital exposure.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">No Market Impact</span>: In a simulation, large
                            positions do not affect price movement or book depth, unlike in real-world trading where
                            order flow dynamics can change execution quality.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">MegaTrader urges users to view the simulation as
                        an exercise in building good habits rather than measuring investable skills. While success on
                        the platform can be a confidence booster, it should not replace comprehensive risk training or
                        professional financial education. Users are strongly discouraged from making real-money trading
                        decisions based solely on simulated success.</p>
                    <p class="leading-normal text-base fw-medium">Performance in a controlled environment should not be
                        used to make financial or investment decisions. Simulated success does not imply or guarantee
                        success in real trading environments.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Conditions for Earnings and
                        Payouts</h3>
                    <p class="leading-normal text-base fw-medium">MegaTrader offers real monetary rewards in certain
                        cases—but only under specific conditions. Payouts and other incentives are provided to users who
                        complete simulation-based evaluations according to established platform rules. Not all users
                        will earn rewards, and there are no guarantees that trading performance will result in a
                        payout.</p>
                    <p class="leading-normal text-base fw-medium">Key points to understand:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Conditional Eligibility</span>: All real rewards are
                            subject to completion of verification procedures, rule compliance, and successful evaluation
                            within the simulation.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Performance-Based Structure</span>: Payouts are based
                            on measurable performance factors including strategy consistency, risk discipline, and
                            adherence to account-specific rules.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Manual Review Required</span>: All payout requests
                            are reviewed by MegaTrader's compliance team before disbursement. We may request additional
                            documentation or perform audits prior to releasing funds.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">No Passive Earnings</span>: Users are not compensated
                            for time, participation, or platform usage alone. Rewards must be earned and are not fixed
                            or recurring.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">MegaTrader maintains full discretion over reward
                        eligibility and retains the right to deny payouts to users who violate terms, exhibit
                        manipulative behavior, or fail verification. While payouts may carry real monetary value, they
                        are derived from performance within a simulated system—not from returns on real capital.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Disclaimer on Financial
                        Advice</h3>
                    <p class="leading-normal text-base fw-medium">No. MegaTrader does not offer or imply the delivery of
                        financial advice, portfolio management services, or investment recommendations. All content and
                        tools available on our platform are provided strictly for informational and educational
                        purposes. Any analytics, performance tracking, strategy feedback, or tutorials are meant to help
                        users better understand the simulated environment—not guide actual investment decisions.</p>
                    <p class="leading-normal text-base fw-medium">Important distinctions:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">No Suitability Analysis</span>: We do not assess your
                            financial background, trading knowledge, or risk tolerance to recommend any strategy or
                            action.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">No Fiduciary Relationship</span>: There is no legal,
                            fiduciary, or advisory relationship created between you and MegaTrader by your use of the
                            simulation tools or any associated material.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">General Information Only</span>: Articles, tutorials,
                            dashboards, and account statistics are general in nature and not tailored to individual
                            needs or goals.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">You are solely responsible for interpreting your
                        simulated performance and deciding how (or whether) to act on any insights gained through the
                        platform. We strongly encourage users to consult a licensed financial advisor before making any
                        real-money decisions based on their experiences within MegaTrader. The platform’s tools are not
                        substitutes for formal financial planning, investment research, or regulated trading advice.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Legal and Regulatory
                        Status</h3>
                    <p class="leading-normal text-base fw-medium">MegaTrader operates entirely outside of regulated
                        financial markets and does not fall under the supervision of securities or commodities
                        authorities such as the SEC, CFTC, FINRA, or NFA. Our services are designed for simulation, not
                        for trading or investing, and do not constitute regulated financial activity under current U.S.
                        or international law.</p>
                    <p class="leading-normal text-base fw-medium">Clarifications:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">No Regulatory Licensure</span>: MegaTrader is not
                            licensed as a broker, advisor, or investment dealer and does not offer execution, clearing,
                            or custodial services.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Not a Financial Institution</span>: We do not accept
                            or manage customer funds for investment purposes, nor do we provide margin accounts or
                            clearing functions.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Voluntary Participation</span>: Your use of
                            MegaTrader is entirely voluntary and does not form a contract or investor relationship with
                            our company.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">No Securities Offered</span>: No shares, tokens, or
                            investment instruments are offered or sold through the platform.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">By accessing our services, you acknowledge that
                        MegaTrader is a private platform for performance evaluation and educational simulation. Users
                        are responsible for understanding that our services do not confer legal standing as a trading
                        firm, asset manager, or capital provider. We advise against confusing MegaTrader’s functionality
                        with any regulated investment activity.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Risk Considerations</h3>
                    <p class="leading-normal text-base fw-medium">Yes. Although MegaTrader operates in a simulation-only
                        environment, users are still exposed to psychological, behavioral, and emotional risks that can
                        affect their trading mindset. The absence of financial loss does not eliminate risk—particularly
                        the risk of developing false confidence or unsustainable trading habits.</p>
                    <p class="leading-normal text-base fw-medium">Consider the following:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Psychological Exposure</span>: Engaging in
                            simulations can foster overconfidence if users assume that simulated success guarantees
                            real-world profitability.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Risk Misjudgment</span>: Simulated environments often
                            lack consequences for poor risk management, which may lead users to ignore key principles
                            like drawdown control or position sizing.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Time Investment</span>: Users may devote significant
                            time and effort to simulation without realistic expectations or understanding of live market
                            complexities.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Spillover Effects</span>: Behaviors formed in a
                            simulation—such as overtrading or ignoring stop-losses—can carry over into real-money
                            trading and result in losses.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">MegaTrader urges users to treat the simulation
                        seriously while recognizing its limitations. Always supplement your experience with additional
                        education and maintain a conservative outlook if transitioning to real-world market
                        participation. Trading involves substantial risk, and there are no guarantees of success in any
                        environment.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Participation Terms and
                        Disclaimers</h3>
                    <p class="leading-normal text-base fw-medium">No. Your use of the MegaTrader platform does not
                        create any contractual rights, obligations, or guarantees of future outcomes. Participation is
                        granted on a discretionary basis and can be modified, revoked, or limited at any time without
                        notice.</p>
                    <p class="leading-normal text-base fw-medium">Clarifications:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">No Employment Relationship</span>: Engaging with
                            MegaTrader does not create an offer of employment, internship, or partnership. You are not
                            entitled to compensation unless formally awarded through a verified achievement.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Non-Binding Terms</span>: Platform features,
                            including payout programs and prize mechanisms, are subject to change and are not guaranteed
                            in perpetuity.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Discretionary Rewards</span>: All awards, including
                            potential payouts or recognition, are conditional upon platform-defined criteria and
                            eligibility checks.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">We retain the right to amend rules, disqualify
                        participants, or suspend features at our discretion. By participating, you agree to abide by
                        evolving platform policies without asserting entitlement to fixed outcomes. MegaTrader is a
                        private environment for skills evaluation and does not offer enforceable contractual benefits to
                        participants.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Use of User Results and
                        Publicity</h3>
                    <p class="leading-normal text-base fw-medium">Yes, MegaTrader may use user results and simulation
                        achievements for marketing purposes, but only under specific circumstances and always in
                        accordance with user privacy rights. Showcasing participant milestones is part of our effort to
                        highlight platform potential and encourage a competitive, transparent environment.</p>
                    <p class="leading-normal text-base fw-medium">Key disclosures include:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Leaderboard Participation</span>: Top-performing
                            users may appear on public or internal leaderboards as part of platform gamification
                            features. These are updated regularly and reflect current simulated metrics.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Testimonial Features</span>: We may request voluntary
                            testimonials from successful users to share experiences or highlight educational value. All
                            published testimonials require consent.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Social Media Showcases</span>: MegaTrader may
                            highlight notable trading streaks, simulation completion milestones, or other user events on
                            social platforms with anonymization or user permission.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Content Creation</span>: From time to time,
                            anonymized case studies or performance breakdowns may be used for internal content, blog
                            articles, or presentations to help educate new users.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">You are never obligated to participate in
                        marketing initiatives, and we will never misrepresent your performance or use your image without
                        explicit permission where legally required. Participation in simulations does not imply a public
                        endorsement or professional competency. You may opt out of any publicity efforts by contacting
                        <a href="mailto:support@megatrader.io" class="text-ffb54d">support@megatrader.io</a>.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">No Affiliation with
                        Exchanges or Brokers</h3>
                    <p class="leading-normal text-base fw-medium">No. MegaTrader is an independent simulation provider
                        and is not affiliated, endorsed, or partnered with any regulated exchange, clearinghouse, or
                        brokerage. Our systems do not interact with, connect to, or mirror any real execution
                        infrastructure such as CME Group, NYSE, NASDAQ, or other global venues.</p>
                    <p class="leading-normal text-base fw-medium">Additional clarifications:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">No Routing or Clearing</span>: We do not route
                            simulated orders through live brokers, and no clearing or settlement occurs.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">No Data Licensing</span>: Any live-like pricing
                            visible on the platform is either delayed or approximated for educational display.
                            MegaTrader does not purchase or distribute real-time market data.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Educational Emulation Only</span>: While the
                            simulation may mimic patterns seen in actual market conditions, the price feeds, fills, and
                            liquidity responses are not based on live exchange APIs.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">We emphasize that participation on our platform
                        does not grant access to real-market infrastructure. Any references to market behavior are
                        illustrative only and are meant to serve as a learning tool—not a representation of actual
                        execution conditions. Users are responsible for understanding that MegaTrader operates in an
                        entirely closed-loop training environment.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Restricted
                        Jurisdictions</h3>
                    <p class="leading-normal text-base fw-medium">MegaTrader complies with international sanctions and
                        U.S. trade restrictions by limiting access to users from certain high-risk jurisdictions. We
                        proactively restrict access from sanctioned territories and monitor usage to prevent
                        circumvention.</p>
                    <p class="leading-normal text-base fw-medium">The following countries are currently restricted:</p>
                    <p class="leading-normal fw-bold text-stone-300">Afghanistan, Central African Republic, Congo
                        (Brazzaville), Congo (Kinshasa), Cuba, Guinea-Bissau, Iran, Iraq, North Korea, Libya, Mali,
                        Russian Federation, Somalia, South Sudan, Sudan, Syria, Yemen, Venezuela</p>
                    <p class="leading-normal text-base fw-medium">Key notes:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">No VPN Circumvention</span>: Attempting to access the
                            platform from a prohibited location using VPNs, proxies, or other anonymization tools is a
                            violation of our Terms of Service.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Automated Monitoring</span>: Our systems use
                            geolocation, IP filtering, and account metadata to detect unauthorized access attempts from
                            restricted jurisdictions.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Enforcement Measures</span>: Accounts found to be in
                            violation will be suspended or terminated, and no payout eligibility will be honored.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">We enforce these restrictions strictly to comply
                        with legal obligations, protect our infrastructure, and maintain platform integrity. Users must
                        confirm they are accessing the platform from an authorized region and agree not to bypass these
                        controls under any circumstance.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Payout Guarantee
                        Limitations</h3>
                    <p class="leading-normal text-base fw-medium">No. MegaTrader does not guarantee that any user will
                        receive a payout, bonus, or other form of compensation, regardless of time spent or simulation
                        performance. All payouts are discretionary, conditional, and subject to rigorous review
                        processes to ensure compliance and authenticity.</p>
                    <p class="leading-normal text-base fw-medium">Important clarifications:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Performance-Dependent</span>: Payouts are awarded
                            only to users who meet clearly defined simulation milestones and risk management criteria.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Manual Review Process</span>: Each payout request is
                            manually reviewed for compliance with platform rules, trading integrity, and identification
                            verification.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Rule Adherence Required</span>: Any breach of
                            rules—even if performance is strong—can disqualify a user from eligibility.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Non-Recurring</span>: A successful payout in one
                            cycle does not entitle a user to future payouts without repeating the full evaluation.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">MegaTrader reserves full discretion to deny,
                        delay, or revoke payout access for any reason, including but not limited to fraud detection,
                        duplicate account activity, or abuse of simulated mechanics. Users should approach simulation as
                        a structured training opportunity—not as a guaranteed income stream.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Conversion of Simulated
                        Accounts</h3>
                    <p class="leading-normal text-base fw-medium">No. MegaTrader does not offer direct account
                        conversions into live trading environments. The platform does not provide brokerage services,
                        and simulation performance does not entitle users to real capital allocation or access to
                        trading accounts connected to live markets.</p>
                    <p class="leading-normal text-base fw-medium">However, users may be offered performance-based
                        incentives such as additional evaluations, platform bonuses, or recognition if they meet
                        specific simulation benchmarks. These incentives do not represent or resemble live trading
                        privileges.</p>
                    <p class="leading-normal text-base fw-medium">Points of clarification:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">No Real Brokerage Transition</span>: We are not
                            affiliated with any brokerages or funding firms that would facilitate such conversions.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Simulation Purposes Only</span>: The platform exists
                            purely for skill assessment, discipline training, and educational analysis.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Potential Offers Are Internal</span>: If additional
                            evaluation paths or opportunities are presented, they remain within the simulated ecosystem.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">Any statements referring to “funded
                        opportunities” or advanced access refer to internal simulation tiers or expanded testing
                        features. We do not provide capital or connect users to real trading infrastructure under any
                        circumstances.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Reporting Concerns or
                        Inaccuracies</h3>
                    <p class="leading-normal text-base fw-medium">MegaTrader is committed to transparency and continuous
                        improvement. If you believe any aspect of this Disclosure Policy is unclear, misleading,
                        incomplete, or out of date, we encourage you to contact us. Your feedback helps us enhance the
                        platform and ensure full compliance with evolving user expectations and legal standards.</p>
                    <p class="leading-normal text-base fw-medium">You may report issues through the following
                        methods:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Live Chat Support</span>: Available directly through
                            our website or user dashboard. A support agent can assist in escalating concerns to our
                            legal or compliance team.
                        </li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                    class="fw-bold text-stone-300">Email Communication</span>: Submit detailed inquiries
                            to <a href="mailto:support@megatrader.io" class="text-ffb54d">support@megatrader.io</a>.
                            Please include your registered email, account ID (if applicable), and a brief description of
                            the issue.
                        </li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">All reports are reviewed by our compliance team
                        and addressed in the order received. While some updates may require internal review, legal
                        consultation, or policy revision cycles, we aim to respond to all feedback within a reasonable
                        time frame. If a correction is warranted, we will revise the document and update the "Last
                        Updated" date accordingly.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cookiesModal" tabindex="-1" aria-labelledby="cookiesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-mega">
        <div class="modal-content">
            <div class="modal-header w-100 border-0 justify-content-between align-items-start p-0">
                <h5 class="modal-title text-white heading-sm-medium text-uppercase" id="cookiesModalLabel">
                    Cookies Settings</h5>
                <button type="button" class="p-0 border-0 bg-transparent shadow-none" data-bs-dismiss="modal"
                        aria-label="Close">
          <span aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                 data-slot="icon" class="text-white w-6 h-6">
              <path fill-rule="evenodd"
                    d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z"
                    clip-rule="evenodd"></path>
            </svg>
          </span>
                </button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center w-100 my-35 px-0">
                <div class="pe-3">
                    <h3 class="text-uppercase text-white leading-7 text-2xl fw-medium">MegaTrader Cookies Policy</h3>
                    <p class="leading-normal text-base fw-medium">This Cookies Policy explains how MegaTrader Holdings Inc. ("MegaTrader," "we," "our," or "us") uses cookies and similar technologies to recognize you when you visit our website or use our services. It explains what these technologies are, why we use them, and your rights to control their use. This policy forms part of our overall Privacy Policy.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Overview of Cookies and Tracking Technologies</h3>
                    <p class="leading-normal text-base fw-medium">Cookies are small text files that websites place on your device to recognize returning users and store information about their browsing habits. These files allow MegaTrader to provide personalized experiences, remember your preferences, and track engagement with our content. Other tracking technologies—such as pixels, beacons, and local storage—may serve similar purposes and operate alongside cookies to collect information about user activity.</p>
                    <p class="leading-normal text-base fw-medium">Cookies are generally categorized as:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Session Cookies</span>, which expire when you close your browser</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Persistent Cookies</span>, which remain on your device until deleted manually or by expiry date</li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">These technologies do not give us access to your device or private information unless explicitly provided by you through forms or settings. They help ensure the smooth functioning of our platform, enhance performance, and allow us to tailor our services to your needs.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Purpose of Cookie Usage</h3>
                    <p class="leading-normal text-base fw-medium">MegaTrader uses cookies and similar tracking tools for a variety of reasons, including:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Authentication and Security</span>: Verifying user identity, securing login sessions, and detecting malicious activity</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Platform Optimization</span>: Measuring loading times, feature usage, and user engagement to enhance website functionality</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Customization</span>: Remembering language settings, display preferences, and user-selected configurations</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Analytics and Performance</span>: Collecting aggregated statistics about usage, session length, and click behavior</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Marketing and Attribution</span>: Measuring the effectiveness of promotional campaigns and affiliate referral activity</li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">We use both first-party cookies (placed by us) and third-party cookies (placed by external services like analytics tools and ad networks). These allow us to monitor traffic patterns and improve user experiences based on aggregated insights.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Types of Cookies Used on MegaTrader</h3>
                    <p class="leading-normal text-base fw-medium">The types of cookies used on the MegaTrader platform include:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Essential Cookies</span>: Required for core functions such as login, security, and navigation. Disabling these may impair your ability to use the site.</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Functional Cookies</span>: Enable advanced features like saving account preferences, interface customization, and remembering last session data.</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Analytical Cookies</span>: Used by services like Google Analytics to understand user behavior, including pages visited, time spent, and referral links.</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Marketing Cookies</span>: Enable performance tracking for ads, referral programs, and partner campaigns. These cookies may follow you across websites.</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Support Cookies</span>: Help our support system recognize returning users, manage ticket flows, and deliver assistance more efficiently.</li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">You can manage your preferences for each type of cookie in our Cookie Settings menu or through browser-level controls.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Sharing of Cookie Data with Third Parties</h3>
                    <p class="leading-normal text-base fw-medium">Yes, some cookies on the MegaTrader platform facilitate limited sharing of data with third-party vendors that provide essential services. These parties help us operate core functions such as analytics, marketing attribution, and customer support.</p>
                    <p class="leading-normal text-base fw-medium">Cookie-related data may be shared in a pseudonymized format that removes direct personal identifiers but allows for trend analysis, performance tracking, and system optimization.</p>
                    <p class="leading-normal text-base fw-medium">Third parties include:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Analytics Providers</span>: Tools like Google Analytics and Hotjar help us understand user engagement, bounce rates, and session flows. These insights help optimize navigation, content design, and user satisfaction.</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Marketing and Referral Tools</span>: These cookies track which partner or promotion led a user to the platform. We use the data to calculate affiliate commissions, evaluate ad performance, and prevent referral fraud.</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Support Platforms</span>: Our support chat and ticketing systems may rely on cookies to recall past conversations, identify users more quickly, and prioritize urgent tickets.</li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">Importantly, all third parties are vetted under strict data privacy terms and are not permitted to reuse, resell, or combine your cookie data with external datasets for profiling or retargeting. Data is used only for operational needs tied directly to MegaTrader’s services. If any vendor fails to uphold these requirements, their access will be revoked immediately.</p>
                    <p class="leading-normal text-base fw-medium">We do not allow cookies that enable third-party behavioral advertising outside of our own domain, and we prohibit cross-site tracking that monetizes your browsing behavior beyond our scope of service.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Managing and Disabling Cookies</h3>
                    <p class="leading-normal text-base fw-medium">You have full control over how cookies are used during your experience on MegaTrader. We provide a multi-layered consent and preference system to ensure transparency and empower your decision-making. From the moment you first visit our site, a cookie consent banner allows you to choose which categories of cookies you accept or reject. You may change these preferences at any time.</p>
                    <p class="leading-normal text-base fw-medium">Control options include:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">On-Site Settings</span>: Use our Cookie Settings panel to accept or deny non-essential cookies. Your preferences are saved per session and device.</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Browser Tools</span>: Most major browsers—such as Chrome, Firefox, Safari, and Edge—offer tools to block, delete, or review cookies. You can configure your browser to reject all cookies or to alert you when a site attempts to place a cookie.</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Third-Party Extensions</span>: Tools like Ghostery, uBlock Origin, or Privacy Badger can give you deeper insight and control over tracking technologies used across the web, including on MegaTrader.</li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">Please be aware that disabling essential cookies will limit core functionality such as login authentication, secure session tracking, and account access. If you disable cookies broadly, some pages may not load properly, and certain user settings may not persist across sessions.</p>
                    <p class="leading-normal text-base fw-medium">We advise selectively disabling only non-essential categories to retain a secure and seamless experience while still exercising privacy preferences.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Cookie Duration and Storage</h3>
                    <p class="leading-normal text-base fw-medium">Cookie storage duration varies based on the type of cookie and the reason it was placed. Each cookie is assigned a lifespan determined by its function, whether it’s session-based or persistent, and by the needs of our platform’s functionality and compliance standards.</p>
                    <p class="leading-normal text-base fw-medium">Here is a general breakdown:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Session Cookies</span>: These are temporary and expire automatically when you close your browser or end your session. They are typically used for login validation, temporary preferences, or secure navigation.</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Persistent Cookies</span>: These remain on your device for a pre-defined period—ranging from a few days to several months—until they expire naturally or are manually deleted. They help remember login credentials (if opted in), maintain dashboard configurations, and track analytics over time.</li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">MegaTrader routinely reviews cookie storage policies and works with third-party partners to ensure retention periods do not exceed necessity. Retention settings are configured with security and privacy in mind, ensuring compliance with data minimization practices under international privacy frameworks.</p>
                    <p class="leading-normal text-base fw-medium">You may delete cookies at any time via your browser or device settings, and doing so will not affect your account status—though it may reset preferences and force a re-authentication step upon your next login.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Use of Cookies in Decision-Making</h3>
                    <p class="leading-normal text-base fw-medium">No. MegaTrader does not engage in automated decision-making or algorithmic profiling based solely on cookie or tracking data. While cookies may influence how certain user interface elements are displayed (such as preferred language, region-specific defaults, or interface layout), they do not drive any consequential decisions regarding account access, eligibility for services, or evaluation outcomes.</p>
                    <p class="leading-normal text-base fw-medium">Every major decision that affects a user’s standing on the platform—such as evaluation results, payout approvals, or account restrictions—is manually reviewed by compliance or support staff. These decisions rely on comprehensive data from your simulation history, adherence to platform rules, and identity verification processes—not cookie data alone.</p>
                    <p class="leading-normal text-base fw-medium">Cookies help optimize user experience but are not connected to our rules enforcement systems. For example, a marketing cookie may influence which offer banner is displayed on your dashboard, but it will never be used to assess your trading performance or eligibility for bonuses.</p>
                    <p class="leading-normal text-base fw-medium">We believe in human accountability and transparency when it comes to decisions that affect user rights. Any concerns about decision-making may be raised with our support team, where a manual review will be conducted upon request.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Security of Cookie Data</h3>
                    <p class="leading-normal text-base fw-medium">MegaTrader takes the protection of your cookie-related data seriously and implements robust technical and organizational measures to prevent unauthorized access, alteration, disclosure, or destruction. We use multiple layers of security protocols to protect data in transit and at rest, consistent with industry best practices and regulatory guidance.</p>
                    <p class="leading-normal text-base fw-medium">Key safeguards include:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">HTTPS Encryption</span>: All communications between your device and MegaTrader’s servers are encrypted using SSL/TLS protocols.</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Cookie Security Attributes</span>: Secure attributes such as HttpOnly, Secure, and SameSite are implemented to prevent unauthorized JavaScript access or cross-site forgery.</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Limited Access Controls</span>: Only trained and authorized personnel can access systems that process cookie-related data, and access is role-based.</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Third-Party Vetting</span>: Vendors who process cookie data on our behalf are subject to privacy and security audits and must meet contractual standards for safeguarding data.</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Ongoing Monitoring and Testing</span>: We regularly scan and audit our systems for vulnerabilities, monitor for suspicious behavior, and apply patches to protect cookie storage mechanisms.</li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">MegaTrader also employs data integrity checks and logging systems to detect tampering or abnormal usage. If any incident occurs involving cookie data, our security team initiates immediate mitigation procedures, and users will be notified in accordance with applicable law.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Changes to Cookie Usage</h3>
                    <p class="leading-normal text-base fw-medium">Yes. The types and purposes of cookies used by MegaTrader may evolve as our platform develops and as regulatory or security standards shift. We regularly review our cookie practices to ensure alignment with user expectations and legal frameworks, and we reserve the right to modify cookie categories, lifespans, or third-party vendors accordingly.</p>
                    <p class="leading-normal text-base fw-medium">We may implement new types of cookies for:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Supporting advanced functionality (e.g., real-time personalization, performance-based content delivery)</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Improving security features that prevent fraud or detect anomalies in usage</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Enabling new marketing or analytics integrations, provided they comply with privacy protections</li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">If material changes are made to the types of cookies we use or their functions, we will:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Update the "Last Updated" date at the top of this policy</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Notify users via banner notices, dashboard alerts, or email if required by law</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Allow users to review and update their cookie preferences in the updated consent banner or settings tool</li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">We encourage users to revisit this policy periodically. Continued use of the platform following changes constitutes acceptance of the updated terms unless you modify your cookie preferences through the available control tools.</p>

                    <h3 class="mt-50 text-uppercase text-white leading-7 text-2xl fw-medium">Contact Information for Cookie Policy</h3>
                    <p class="leading-normal text-base fw-medium">If you have any concerns, feedback, or questions regarding this Cookies Policy, or how MegaTrader handles your cookie preferences, we encourage you to contact our support team directly through one of the following methods:</p>
                    <ul class="list-disc text-stone-400 d-flex flex-column gap-2">
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Live Support Chat</span>: Open a ticket through the chat widget on our website or dashboard. A support agent will follow up during business hours.</li>
                        <li class="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span class="fw-bold text-stone-300">Email Assistance</span>: Reach us at <a href="mailto:support@megatrader.io" class="text-ffb54d">support@megatrader.io</a> with any cookie-related questions. Please include your account email and, if applicable, reference any cookie preference issues.</li>
                    </ul>
                    <p class="leading-normal text-base fw-medium mt-3">We strive to respond within 1–3 business days depending on the nature of the request. If your inquiry requires input from our privacy or security officers, a detailed response may take slightly longer.</p>
                    <p class="leading-normal text-base fw-medium">Your trust is important to us. All inquiries are handled with confidentiality and care, and we are happy to assist you with preference changes, opt-out processes, or general clarifications. By contacting us, you help us improve transparency and uphold our commitment to privacy best practices.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function ($) {
        function showModal(modalId) {
            $('#modalOverlay').removeClass('hidden');
            $(modalId).removeClass('hidden');
        }

        function hideModal() {
            $('#modalOverlay, .custom-modal').addClass('hidden');
        }

        $('#openTermsModal').on('click', function (e) {
            e.preventDefault();
            showModal('#termsModal');
        });

        $('#openPrivacyModal').on('click', function (e) {
            e.preventDefault();
            showModal('#privacyModal');
        });

        $('.close-modal, #modalOverlay').on('click', function () {
            hideModal();
        });
    });
</script>


<!-- Copy Right Area End -->

<?php wp_footer(); ?>
</body>

</html>
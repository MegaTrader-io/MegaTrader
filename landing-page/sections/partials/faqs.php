<?php
function render_faqs($defaultCategoryId = '', array $faqsGroup = []): void
{
    foreach ($faqsGroup as $faqData) {
        $categoryId = $faqData['id'] ?? '';
        $faqs = $faqData['faqs'] ?? [];

        echo '<div class="mx-auto max-w-[1030px] space-y-8 ' . ($categoryId != $defaultCategoryId ? 'hidden' : '') . '" data-category="' . esc_attr($categoryId) . '">';

        foreach ($faqs as $index => $faq) {
            $radioCategoryGroup = 'radio-' . $categoryId;
            $questionId = 'id-' . $categoryId . '-' . ($index + 1);
            $question = $faq['question'] ?? '';
            $answer = $faq['answer'] ?? '';

            ?>
            <label for="<?= $questionId ?>"
                   class="px-4 cursor-pointer relative flex w-full items-center flex-col text-left">
                <input type="radio" name="<?= $radioCategoryGroup ?>" id="<?= $questionId ?>"
                       class="peer/faq-question hidden" <?= $index == 0 ? 'checked' : '' ?> />
                <div class="block peer-checked/faq-question:hidden absolute right-4 top-1">
                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_8223_24371" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                              y="0"
                              width="30" height="30">
                            <rect width="30" height="30" fill="#D9D9D9"></rect>
                        </mask>
                        <g mask="url(#mask0_8223_24371)">
                            <path d="M13.75 16.25H6.25V13.75H13.75V6.25H16.25V13.75H23.75V16.25H16.25V23.75H13.75V16.25Z"
                                  fill="white"></path>
                        </g>
                    </svg>
                </div>
                <div class="hidden peer-checked/faq-question:block absolute right-4 top-1">
                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_8280_2980" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                              y="0"
                              width="30" height="30">
                            <rect width="30" height="30" fill="#D9D9D9"></rect>
                        </mask>
                        <g mask="url(#mask0_8280_2980)">
                            <path d="M6.25 16.25V13.75H23.75V16.25H6.25Z" fill="white"></path>
                        </g>
                    </svg>
                </div>
                <div class="flex w-full items-center justify-between">
                    <div class="text-white text-xl font-light w-full leading-6 uppercase mr-[30px] py-2 select-none">
                        <?= esc_html($question) ?>
                    </div>
                </div>
                <div class="hidden peer-checked/faq-question:block text-stone-400 justify-start text-base font-medium leading-6 pt-2">
                    <?= esc_html($answer) ?>
                </div>
            </label>
            <?php
        }

        echo '</div>';
    }
}

?>


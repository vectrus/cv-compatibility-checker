jQuery(document).ready(function ($) {
    $('#job-description-form').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $results = $('#analysis-results');
        const $resultsContent = $results.find('.results-content');
        const $debugOutput = $('#debug-output');
        const $debugContent = $debugOutput.find('.debug-content');
        const $submitButton = $form.find('.submit-button');

        // Show loading state
        $submitButton.prop('disabled', true).text('Bezig met analyseren...');
        $results.addClass('hidden');
        $debugOutput.addClass('hidden');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'analyze_compatibility',
                job_description: $('#job-description').val()
            },
            success: function (response) {
                if (response.success) {
                    $resultsContent.html(formatAnalysisResults(response.data));
                    $results.removeClass('hidden');
                } else {
                    $resultsContent.html(
                        `<div class="error-message">Fout: ${response.error || 'Er is een onbekende fout opgetreden'}</div>`
                    );
                    $results.removeClass('hidden');
                }

                // Handle debug information if available
                if (response.debug) {
                    $debugContent.html(JSON.stringify(response.debug, null, 2));
                    $debugOutput.removeClass('hidden');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $resultsContent.html(
                    `<div class="error-message">
                        Ajax Error: ${textStatus}<br>
                        Server Response: ${errorThrown}<br>
                        Please check the browser console for more details.
                    </div>`
                );
                $results.removeClass('hidden');
                console.error('Ajax Error:', {
                    status: textStatus,
                    error: errorThrown,
                    response: jqXHR.responseText
                });
            },
            complete: function () {
                $submitButton.prop('disabled', false).text('Analyse Compabiliteit');
            }
        });
    });

    function formatAnalysisResults(data) {
        if (typeof data === 'string') {
            return `<div class="analysis-content">${data}</div>`;
        }

        return `
            <div class="compatibility-score">
                ${data.score ? `Overall Compatibility: ${data.score}` : ''}
            </div>
            <div class="matching-skills">
                <h4>Matching Skills and Qualifications:</h4>
                ${data.matching_skills || ''}
            </div>
            <div class="missing-skills">
                <h4>Missing Requirements:</h4>
                ${data.missing_skills || ''}
            </div>
            <div class="recommendations">
                <h4>Recommendations:</h4>
                ${data.recommendations || ''}
            </div>
        `;
    }
});
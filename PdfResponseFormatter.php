<?php
/**
 *
 * @author Vladimir Smirnov <uranetz@obregon.co>
 * @created 15/01/22 10:27 PM
 */

namespace uranetz\pdf;

use Yii;
use yii\base\Component;
use yii\web\Response;
use yii\web\ResponseFormatterInterface;

/**
 * PdfResponseFormatter formats the given HTML data into a PDF response content.
 *
 * It is used by [[Response]] to format response data.
 *
 * @since 2.0
 */
class PdfResponseFormatter extends Component implements ResponseFormatterInterface
{
    public $mode = '';

    public $format = 'A4';

    public $defaultFontSize = 0;

    public $defaultFont = '';

    public $marginLeft = 15;

    public $marginRight = 15;

    public $marginTop = 16;

    public $marginBottom = 16;

    public $marginHeader = 9;

    public $marginFooter = 9;

    /**
     * @var string 'Landscape' or 'Portrait'
     * Default to 'Portrait'
     */
    public $orientation = 'P';

    public $options = [];

    /**
     * @var Closure function($mpdf, $data){}
     */
    public $beforeRender;

    /**
     * Formats the specified response.
     *
     * @param Response $response the response to be formatted.
     */
    public function format($response)
    {
        $response->getHeaders()->set('Content-Type', 'application/pdf');
        $response->content = $this->formatPdf($response);
    }

    /**
     * Formats response HTML in PDF
     *
     * @param Response $response
     */
    protected function formatPdf($response)
    {
        $mpdf = new \Mpdf\Mpdf([
            'mode' => $this->mode,
            'format' => $this->format,
            'default_font_size' => $this->defaultFontSize,
            'default_font' => $this->defaultFont,
            'margin_left' => $this->marginLeft,
            'margin_right' => $this->marginRight,
            'margin_top' => $this->marginTop,
            'margin_bottom' => $this->marginBottom,
            'margin_header' => $this->marginHeader,
            'margin_footer' => $this->marginFooter,
            'orientation' => $this->orientation,
            ]
        );

        foreach ($this->options as $key => $option) {
            $mpdf->$key = $option;
        }

        if ($this->beforeRender instanceof \Closure) {
            call_user_func($this->beforeRender, $mpdf, $response->data);
        }

        $mpdf->WriteHTML($response->data);
        return $mpdf->Output('', 'S');
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-3-26
 * Time: 0:53
 */
namespace Validator\Containers;

use Constant\Project;
use Tool\BaseContainer;
use Validator\Impl\Double\DoubleBetween;
use Validator\Impl\Double\DoubleMax;
use Validator\Impl\Double\DoubleMin;
use Validator\Impl\Double\DoubleRequired;
use Validator\Impl\Int\IntBetween;
use Validator\Impl\Int\IntIn;
use Validator\Impl\Int\IntMax;
use Validator\Impl\Int\IntMin;
use Validator\Impl\Int\IntRequired;
use Validator\Impl\String\StringAlnum;
use Validator\Impl\String\StringAlpha;
use Validator\Impl\String\StringBaseImage;
use Validator\Impl\String\StringDigit;
use Validator\Impl\String\StringDigitLower;
use Validator\Impl\String\StringDigitUpper;
use Validator\Impl\String\StringEmail;
use Validator\Impl\String\StringIP;
use Validator\Impl\String\StringJson;
use Validator\Impl\String\StringLat;
use Validator\Impl\String\StringLng;
use Validator\Impl\String\StringLower;
use Validator\Impl\String\StringMax;
use Validator\Impl\String\StringMin;
use Validator\Impl\String\StringNoEmoji;
use Validator\Impl\String\StringNoJs;
use Validator\Impl\String\StringPhone;
use Validator\Impl\String\StringRegex;
use Validator\Impl\String\StringRequired;
use Validator\Impl\String\StringSign;
use Validator\Impl\String\StringTel;
use Validator\Impl\String\StringUpper;
use Validator\Impl\String\StringUrl;
use Validator\Impl\String\StringZh;

class ValidatorContainer extends BaseContainer {
    public function __construct() {
        $this->registryMap = [
            Project::VALIDATOR_INT_TYPE_REQUIRED,
            Project::VALIDATOR_INT_TYPE_MIN,
            Project::VALIDATOR_INT_TYPE_MAX,
            Project::VALIDATOR_INT_TYPE_IN,
            Project::VALIDATOR_INT_TYPE_BETWEEN,
            Project::VALIDATOR_DOUBLE_TYPE_REQUIRED,
            Project::VALIDATOR_DOUBLE_TYPE_BETWEEN,
            Project::VALIDATOR_DOUBLE_TYPE_MIN,
            Project::VALIDATOR_DOUBLE_TYPE_MAX,
            Project::VALIDATOR_STRING_TYPE_REQUIRED,
            Project::VALIDATOR_STRING_TYPE_MIN,
            Project::VALIDATOR_STRING_TYPE_MAX,
            Project::VALIDATOR_STRING_TYPE_REGEX,
            Project::VALIDATOR_STRING_TYPE_PHONE,
            Project::VALIDATOR_STRING_TYPE_TEL,
            Project::VALIDATOR_STRING_TYPE_EMAIL,
            Project::VALIDATOR_STRING_TYPE_URL,
            Project::VALIDATOR_STRING_TYPE_JSON,
            Project::VALIDATOR_STRING_TYPE_SIGN,
            Project::VALIDATOR_STRING_TYPE_BASE_IMAGE,
            Project::VALIDATOR_STRING_TYPE_IP,
            Project::VALIDATOR_STRING_TYPE_LNG,
            Project::VALIDATOR_STRING_TYPE_LAT,
            Project::VALIDATOR_STRING_TYPE_NO_JS,
            Project::VALIDATOR_STRING_TYPE_NO_EMOJI,
            Project::VALIDATOR_STRING_TYPE_ZH,
            Project::VALIDATOR_STRING_TYPE_ALNUM,
            Project::VALIDATOR_STRING_TYPE_ALPHA,
            Project::VALIDATOR_STRING_TYPE_DIGIT,
            Project::VALIDATOR_STRING_TYPE_LOWER,
            Project::VALIDATOR_STRING_TYPE_UPPER,
            Project::VALIDATOR_STRING_TYPE_DIGIT_LOWER,
            Project::VALIDATOR_STRING_TYPE_DIGIT_UPPER,
        ];

        $this->bind(Project::VALIDATOR_INT_TYPE_REQUIRED, function () {
            return new IntRequired();
        });

        $this->bind(Project::VALIDATOR_INT_TYPE_MIN, function () {
            return new IntMin();
        });

        $this->bind(Project::VALIDATOR_INT_TYPE_MAX, function () {
            return new IntMax();
        });

        $this->bind(Project::VALIDATOR_INT_TYPE_IN, function () {
            return new IntIn();
        });

        $this->bind(Project::VALIDATOR_INT_TYPE_BETWEEN, function () {
            return new IntBetween();
        });

        $this->bind(Project::VALIDATOR_DOUBLE_TYPE_REQUIRED, function () {
            return new DoubleRequired();
        });

        $this->bind(Project::VALIDATOR_DOUBLE_TYPE_BETWEEN, function () {
            return new DoubleBetween();
        });

        $this->bind(Project::VALIDATOR_DOUBLE_TYPE_MIN, function () {
            return new DoubleMin();
        });

        $this->bind(Project::VALIDATOR_DOUBLE_TYPE_MAX, function () {
            return new DoubleMax();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_REQUIRED, function () {
            return new StringRequired();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_MIN, function () {
            return new StringMin();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_MAX, function () {
            return new StringMax();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_REGEX, function () {
            return new StringRegex();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_PHONE, function () {
            return new StringPhone();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_TEL, function () {
            return new StringTel();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_EMAIL, function () {
            return new StringEmail();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_URL, function () {
            return new StringUrl();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_JSON, function () {
            return new StringJson();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_SIGN, function () {
            return new StringSign();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_BASE_IMAGE, function () {
            return new StringBaseImage();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_IP, function () {
            return new StringIP();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_LNG, function () {
            return new StringLng();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_LAT, function () {
            return new StringLat();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_NO_JS, function () {
            return new StringNoJs();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_NO_EMOJI, function () {
            return new StringNoEmoji();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_ZH, function () {
            return new StringZh();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_ALNUM, function () {
            return new StringAlnum();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_ALPHA, function () {
            return new StringAlpha();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_DIGIT, function () {
            return new StringDigit();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_LOWER, function () {
            return new StringLower();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_UPPER, function () {
            return new StringUpper();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_DIGIT_LOWER, function () {
            return new StringDigitLower();
        });

        $this->bind(Project::VALIDATOR_STRING_TYPE_DIGIT_UPPER, function () {
            return new StringDigitUpper();
        });
    }
}
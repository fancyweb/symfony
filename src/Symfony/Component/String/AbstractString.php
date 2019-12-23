<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\String;

use Symfony\Component\String\Exception\ExceptionInterface;
use Symfony\Component\String\Exception\InvalidArgumentException;
use Symfony\Component\String\Exception\RuntimeException;

/**
 * Represents a string of abstract characters.
 *
 * Unicode defines 3 types of "characters" (bytes, code points and grapheme clusters).
 * This class is the abstract type to use as a type-hint when the logic you want to
 * implement doesn't care about the exact variant it deals with.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 * @author Hugo Hamon <hugohamon@neuf.fr>
 *
 * @throws ExceptionInterface
 *
 * @experimental in 5.0
 */
abstract class AbstractString implements \JsonSerializable
{
    public const PREG_PATTERN_ORDER = \PREG_PATTERN_ORDER;
    public const PREG_SET_ORDER = \PREG_SET_ORDER;
    public const PREG_OFFSET_CAPTURE = \PREG_OFFSET_CAPTURE;
    public const PREG_UNMATCHED_AS_NULL = \PREG_UNMATCHED_AS_NULL;

    public const PREG_SPLIT = 0;
    public const PREG_SPLIT_NO_EMPTY = \PREG_SPLIT_NO_EMPTY;
    public const PREG_SPLIT_DELIM_CAPTURE = \PREG_SPLIT_DELIM_CAPTURE;
    public const PREG_SPLIT_OFFSET_CAPTURE = \PREG_SPLIT_OFFSET_CAPTURE;

    /**
     * Map English plural to singular suffixes.
     *
     * @see http://english-zone.com/spelling/plurals.html
     */
    private static $pluralMap = [
        // First entry: plural suffix, reversed
        // Second entry: length of plural suffix
        // Third entry: Whether the suffix may succeed a vocal
        // Fourth entry: Whether the suffix may succeed a consonant
        // Fifth entry: singular suffix, normal

        // bacteria (bacterium), criteria (criterion), phenomena (phenomenon)
        ['a', 1, true, true, ['on', 'um']],

        // nebulae (nebula)
        ['ea', 2, true, true, 'a'],

        // services (service)
        ['secivres', 8, true, true, 'service'],

        // mice (mouse), lice (louse)
        ['eci', 3, false, true, 'ouse'],

        // geese (goose)
        ['esee', 4, false, true, 'oose'],

        // fungi (fungus), alumni (alumnus), syllabi (syllabus), radii (radius)
        ['i', 1, true, true, 'us'],

        // men (man), women (woman)
        ['nem', 3, true, true, 'man'],

        // children (child)
        ['nerdlihc', 8, true, true, 'child'],

        // oxen (ox)
        ['nexo', 4, false, false, 'ox'],

        // indices (index), appendices (appendix), prices (price)
        ['seci', 4, false, true, ['ex', 'ix', 'ice']],

        // selfies (selfie)
        ['seifles', 7, true, true, 'selfie'],

        // movies (movie)
        ['seivom', 6, true, true, 'movie'],

        // feet (foot)
        ['teef', 4, true, true, 'foot'],

        // geese (goose)
        ['eseeg', 5, true, true, 'goose'],

        // teeth (tooth)
        ['hteet', 5, true, true, 'tooth'],

        // news (news)
        ['swen', 4, true, true, 'news'],

        // series (series)
        ['seires', 6, true, true, 'series'],

        // babies (baby)
        ['sei', 3, false, true, 'y'],

        // accesses (access), addresses (address), kisses (kiss)
        ['sess', 4, true, false, 'ss'],

        // analyses (analysis), ellipses (ellipsis), fungi (fungus),
        // neuroses (neurosis), theses (thesis), emphases (emphasis),
        // oases (oasis), crises (crisis), houses (house), bases (base),
        // atlases (atlas)
        ['ses', 3, true, true, ['s', 'se', 'sis']],

        // objectives (objective), alternative (alternatives)
        ['sevit', 5, true, true, 'tive'],

        // drives (drive)
        ['sevird', 6, false, true, 'drive'],

        // lives (life), wives (wife)
        ['sevi', 4, false, true, 'ife'],

        // moves (move)
        ['sevom', 5, true, true, 'move'],

        // hooves (hoof), dwarves (dwarf), elves (elf), leaves (leaf), caves (cave), staves (staff)
        ['sev', 3, true, true, ['f', 've', 'ff']],

        // axes (axis), axes (ax), axes (axe)
        ['sexa', 4, false, false, ['ax', 'axe', 'axis']],

        // indexes (index), matrixes (matrix)
        ['sex', 3, true, false, 'x'],

        // quizzes (quiz)
        ['sezz', 4, true, false, 'z'],

        // bureaus (bureau)
        ['suae', 4, false, true, 'eau'],

        // fees (fee), trees (tree), employees (employee)
        ['see', 3, true, true, 'ee'],

        // roses (rose), garages (garage), cassettes (cassette),
        // waltzes (waltz), heroes (hero), bushes (bush), arches (arch),
        // shoes (shoe)
        ['se', 2, true, true, ['', 'e']],

        // tags (tag)
        ['s', 1, true, true, ''],

        // chateaux (chateau)
        ['xuae', 4, false, true, 'eau'],

        // people (person)
        ['elpoep', 6, true, true, 'person'],
    ];

    /**
     * Map English singular to plural suffixes.
     *
     * @see http://english-zone.com/spelling/plurals.html
     */
    private static $singularMap = [
        // First entry: singular suffix, reversed
        // Second entry: length of singular suffix
        // Third entry: Whether the suffix may succeed a vocal
        // Fourth entry: Whether the suffix may succeed a consonant
        // Fifth entry: plural suffix, normal

        // criterion (criteria)
        ['airetirc', 8, false, false, 'criterion'],

        // nebulae (nebula)
        ['aluben', 6, false, false, 'nebulae'],

        // children (child)
        ['dlihc', 5, true, true, 'children'],

        // prices (price)
        ['eci', 3, false, true, 'ices'],

        // services (service)
        ['ecivres', 7, true, true, 'services'],

        // lives (life), wives (wife)
        ['efi', 3, false, true, 'ives'],

        // selfies (selfie)
        ['eifles', 6, true, true, 'selfies'],

        // movies (movie)
        ['eivom', 5, true, true, 'movies'],

        // lice (louse)
        ['esuol', 5, false, true, 'lice'],

        // mice (mouse)
        ['esuom', 5, false, true, 'mice'],

        // geese (goose)
        ['esoo', 4, false, true, 'eese'],

        // houses (house), bases (base)
        ['es', 2, true, true, 'ses'],

        // geese (goose)
        ['esoog', 5, true, true, 'geese'],

        // caves (cave)
        ['ev', 2, true, true, 'ves'],

        // drives (drive)
        ['evird', 5, false, true, 'drives'],

        // objectives (objective), alternative (alternatives)
        ['evit', 4, true, true, 'tives'],

        // moves (move)
        ['evom', 4, true, true, 'moves'],

        // staves (staff)
        ['ffats', 5, true, true, 'staves'],

        // hooves (hoof), dwarves (dwarf), elves (elf), leaves (leaf)
        ['ff', 2, true, true, 'ffs'],

        // hooves (hoof), dwarves (dwarf), elves (elf), leaves (leaf)
        ['f', 1, true, true, ['fs', 'ves']],

        // arches (arch)
        ['hc', 2, true, true, 'ches'],

        // bushes (bush)
        ['hs', 2, true, true, 'shes'],

        // teeth (tooth)
        ['htoot', 5, true, true, 'teeth'],

        // bacteria (bacterium), criteria (criterion), phenomena (phenomenon)
        ['mu', 2, true, true, 'a'],

        // men (man), women (woman)
        ['nam', 3, true, true, 'men'],

        // people (person)
        ['nosrep', 6, true, true, ['persons', 'people']],

        // bacteria (bacterium), criteria (criterion), phenomena (phenomenon)
        ['noi', 3, true, true, 'ions'],

        // seasons (season), treasons (treason), poisons (poison), lessons (lesson)
        ['nos', 3, true, true, 'sons'],

        // bacteria (bacterium), criteria (criterion), phenomena (phenomenon)
        ['no', 2, true, true, 'a'],

        // echoes (echo)
        ['ohce', 4, true, true, 'echoes'],

        // heroes (hero)
        ['oreh', 4, true, true, 'heroes'],

        // atlases (atlas)
        ['salta', 5, true, true, 'atlases'],

        // irises (iris)
        ['siri', 4, true, true, 'irises'],

        // analyses (analysis), ellipses (ellipsis), neuroses (neurosis)
        // theses (thesis), emphases (emphasis), oases (oasis),
        // crises (crisis)
        ['sis', 3, true, true, 'ses'],

        // accesses (access), addresses (address), kisses (kiss)
        ['ss', 2, true, false, 'sses'],

        // syllabi (syllabus)
        ['suballys', 8, true, true, 'syllabi'],

        // buses (bus)
        ['sub', 3, true, true, 'buses'],

        // circuses (circus)
        ['suc', 3, true, true, 'cuses'],

        // fungi (fungus), alumni (alumnus), syllabi (syllabus), radii (radius)
        ['su', 2, true, true, 'i'],

        // news (news)
        ['swen', 4, true, true, 'news'],

        // feet (foot)
        ['toof', 4, true, true, 'feet'],

        // chateaux (chateau), bureaus (bureau)
        ['uae', 3, false, true, ['eaus', 'eaux']],

        // oxen (ox)
        ['xo', 2, false, false, 'oxen'],

        // hoaxes (hoax)
        ['xaoh', 4, true, false, 'hoaxes'],

        // indices (index)
        ['xedni', 5, false, true, ['indicies', 'indexes']],

        // boxes (box)
        ['xo', 2, false, true, 'oxes'],

        // indexes (index), matrixes (matrix)
        ['x', 1, true, false, ['cies', 'xes']],

        // appendices (appendix)
        ['xi', 2, false, true, 'ices'],

        // babies (baby)
        ['y', 1, false, true, 'ies'],

        // quizzes (quiz)
        ['ziuq', 4, true, false, 'quizzes'],

        // waltzes (waltz)
        ['z', 1, true, true, 'zes'],
    ];

    /**
     * A list of words which should not be inflected, reversed.
     */
    private static $uninflected = [
        'atad',
        'reed',
        'kcabdeef',
        'hsif',
        'ofni',
        'esoom',
        'seires',
        'peehs',
        'seiceps',
    ];

    protected $string = '';
    protected $ignoreCase = false;

    abstract public function __construct(string $string = '');

    /**
     * Unwraps instances of AbstractString back to strings.
     *
     * @return string[]|array
     */
    public static function unwrap(array $values): array
    {
        foreach ($values as $k => $v) {
            if ($v instanceof self) {
                $values[$k] = $v->__toString();
            } elseif (\is_array($v) && $values[$k] !== $v = static::unwrap($v)) {
                $values[$k] = $v;
            }
        }

        return $values;
    }

    /**
     * Wraps (and normalizes) strings in instances of AbstractString.
     *
     * @return static[]|array
     */
    public static function wrap(array $values): array
    {
        $i = 0;
        $keys = null;

        foreach ($values as $k => $v) {
            if (\is_string($k) && '' !== $k && $k !== $j = (string) new static($k)) {
                $keys = $keys ?? array_keys($values);
                $keys[$i] = $j;
            }

            if (\is_string($v)) {
                $values[$k] = new static($v);
            } elseif (\is_array($v) && $values[$k] !== $v = static::wrap($v)) {
                $values[$k] = $v;
            }

            ++$i;
        }

        return null !== $keys ? array_combine($keys, $values) : $values;
    }

    /**
     * @param string|string[] $needle
     *
     * @return static
     */
    public function after($needle, bool $includeNeedle = false, int $offset = 0): self
    {
        $str = clone $this;
        $str->string = '';
        $i = \PHP_INT_MAX;

        foreach ((array) $needle as $n) {
            $n = (string) $n;
            $j = $this->indexOf($n, $offset);

            if (null !== $j && $j < $i) {
                $i = $j;
                $str->string = $n;
            }
        }

        if (\PHP_INT_MAX === $i) {
            return $str;
        }

        if (!$includeNeedle) {
            $i += $str->length();
        }

        return $this->slice($i);
    }

    /**
     * @param string|string[] $needle
     *
     * @return static
     */
    public function afterLast($needle, bool $includeNeedle = false, int $offset = 0): self
    {
        $str = clone $this;
        $str->string = '';
        $i = null;

        foreach ((array) $needle as $n) {
            $n = (string) $n;
            $j = $this->indexOfLast($n, $offset);

            if (null !== $j && $j >= $i) {
                $i = $offset = $j;
                $str->string = $n;
            }
        }

        if (null === $i) {
            return $str;
        }

        if (!$includeNeedle) {
            $i += $str->length();
        }

        return $this->slice($i);
    }

    /**
     * @return static
     */
    abstract public function append(string ...$suffix): self;

    /**
     * @param string|string[] $needle
     *
     * @return static
     */
    public function before($needle, bool $includeNeedle = false, int $offset = 0): self
    {
        $str = clone $this;
        $str->string = '';
        $i = \PHP_INT_MAX;

        foreach ((array) $needle as $n) {
            $n = (string) $n;
            $j = $this->indexOf($n, $offset);

            if (null !== $j && $j < $i) {
                $i = $j;
                $str->string = $n;
            }
        }

        if (\PHP_INT_MAX === $i) {
            return $str;
        }

        if ($includeNeedle) {
            $i += $str->length();
        }

        return $this->slice(0, $i);
    }

    /**
     * @param string|string[] $needle
     *
     * @return static
     */
    public function beforeLast($needle, bool $includeNeedle = false, int $offset = 0): self
    {
        $str = clone $this;
        $str->string = '';
        $i = null;

        foreach ((array) $needle as $n) {
            $n = (string) $n;
            $j = $this->indexOfLast($n, $offset);

            if (null !== $j && $j >= $i) {
                $i = $offset = $j;
                $str->string = $n;
            }
        }

        if (null === $i) {
            return $str;
        }

        if ($includeNeedle) {
            $i += $str->length();
        }

        return $this->slice(0, $i);
    }

    /**
     * @return int[]
     */
    public function bytesAt(int $offset): array
    {
        $str = $this->slice($offset, 1);

        return '' === $str->string ? [] : array_values(unpack('C*', $str->string));
    }

    /**
     * @return static
     */
    abstract public function camel(): self;

    /**
     * @return static[]
     */
    abstract public function chunk(int $length = 1): array;

    /**
     * @return static
     */
    public function collapseWhitespace(): self
    {
        $str = clone $this;
        $str->string = trim(preg_replace('/(?:\s{2,}+|[^\S ])/', ' ', $str->string));

        return $str;
    }

    /**
     * @param string|string[] $suffix
     */
    public function endsWith($suffix): bool
    {
        if (!\is_array($suffix) && !$suffix instanceof \Traversable) {
            throw new \TypeError(sprintf('Method "%s()" must be overridden by class "%s" to deal with non-iterable values.', __FUNCTION__, \get_class($this)));
        }

        foreach ($suffix as $s) {
            if ($this->endsWith((string) $s)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return static
     */
    public function ensureEnd(string $suffix): self
    {
        if (!$this->endsWith($suffix)) {
            return $this->append($suffix);
        }

        $suffix = preg_quote($suffix);
        $regex = '{('.$suffix.')(?:'.$suffix.')++$}D';

        return $this->replaceMatches($regex.($this->ignoreCase ? 'i' : ''), '$1');
    }

    /**
     * @return static
     */
    public function ensureStart(string $prefix): self
    {
        $prefix = new static($prefix);

        if (!$this->startsWith($prefix)) {
            return $this->prepend($prefix);
        }

        $str = clone $this;
        $i = $prefixLen = $prefix->length();

        while ($this->indexOf($prefix, $i) === $i) {
            $str = $str->slice($prefixLen);
            $i += $prefixLen;
        }

        return $str;
    }

    /**
     * @param string|string[] $string
     */
    public function equalsTo($string): bool
    {
        if (!\is_array($string) && !$string instanceof \Traversable) {
            throw new \TypeError(sprintf('Method "%s()" must be overridden by class "%s" to deal with non-iterable values.', __FUNCTION__, \get_class($this)));
        }

        foreach ($string as $s) {
            if ($this->equalsTo((string) $s)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return static
     */
    abstract public function folded(): self;

    /**
     * @return static
     */
    public function ignoreCase(): self
    {
        $str = clone $this;
        $str->ignoreCase = true;

        return $str;
    }

    /**
     * @param string|string[] $needle
     */
    public function indexOf($needle, int $offset = 0): ?int
    {
        if (!\is_array($needle) && !$needle instanceof \Traversable) {
            throw new \TypeError(sprintf('Method "%s()" must be overridden by class "%s" to deal with non-iterable values.', __FUNCTION__, \get_class($this)));
        }

        $i = \PHP_INT_MAX;

        foreach ($needle as $n) {
            $j = $this->indexOf((string) $n, $offset);

            if (null !== $j && $j < $i) {
                $i = $j;
            }
        }

        return \PHP_INT_MAX === $i ? null : $i;
    }

    /**
     * @param string|string[] $needle
     */
    public function indexOfLast($needle, int $offset = 0): ?int
    {
        if (!\is_array($needle) && !$needle instanceof \Traversable) {
            throw new \TypeError(sprintf('Method "%s()" must be overridden by class "%s" to deal with non-iterable values.', __FUNCTION__, \get_class($this)));
        }

        $i = null;

        foreach ($needle as $n) {
            $j = $this->indexOfLast((string) $n, $offset);

            if (null !== $j && $j >= $i) {
                $i = $offset = $j;
            }
        }

        return $i;
    }

    public function isEmpty(): bool
    {
        return '' === $this->string;
    }

    /**
     * @return static
     */
    abstract public function join(array $strings, string $lastGlue = null): self;

    public function jsonSerialize(): string
    {
        return $this->string;
    }

    abstract public function length(): int;

    /**
     * @return static
     */
    abstract public function lower(): self;

    /**
     * Matches the string using a regular expression.
     *
     * Pass PREG_PATTERN_ORDER or PREG_SET_ORDER as $flags to get all occurrences matching the regular expression.
     *
     * @return array All matches in a multi-dimensional array ordered according to flags
     */
    abstract public function match(string $regexp, int $flags = 0, int $offset = 0): array;

    /**
     * @return static
     */
    abstract public function padBoth(int $length, string $padStr = ' '): self;

    /**
     * @return static
     */
    abstract public function padEnd(int $length, string $padStr = ' '): self;

    /**
     * @return static
     */
    abstract public function padStart(int $length, string $padStr = ' '): self;

    /**
     * @return static
     */
    abstract public function prepend(string ...$prefix): self;

    /**
     * @return static
     */
    public function repeat(int $multiplier): self
    {
        if (0 > $multiplier) {
            throw new InvalidArgumentException(sprintf('Multiplier must be positive, %d given.', $multiplier));
        }

        $str = clone $this;
        $str->string = str_repeat($str->string, $multiplier);

        return $str;
    }

    /**
     * @return static
     */
    abstract public function replace(string $from, string $to): self;

    /**
     * @param string|callable $to
     *
     * @return static
     */
    abstract public function replaceMatches(string $fromRegexp, $to): self;

    /**
     * @return static
     */
    abstract public function slice(int $start = 0, int $length = null): self;

    /**
     * @return static
     */
    abstract public function snake(): self;

    /**
     * @return static
     */
    abstract public function splice(string $replacement, int $start = 0, int $length = null): self;

    /**
     * @return static[]
     */
    public function split(string $delimiter, int $limit = null, int $flags = null): array
    {
        if (null === $flags) {
            throw new \TypeError('Split behavior when $flags is null must be implemented by child classes.');
        }

        if ($this->ignoreCase) {
            $delimiter .= 'i';
        }

        set_error_handler(static function ($t, $m) { throw new InvalidArgumentException($m); });

        try {
            if (false === $chunks = preg_split($delimiter, $this->string, $limit, $flags)) {
                $lastError = preg_last_error();

                foreach (get_defined_constants(true)['pcre'] as $k => $v) {
                    if ($lastError === $v && '_ERROR' === substr($k, -6)) {
                        throw new RuntimeException('Splitting failed with '.$k.'.');
                    }
                }

                throw new RuntimeException('Splitting failed with unknown error code.');
            }
        } finally {
            restore_error_handler();
        }

        $str = clone $this;

        if (self::PREG_SPLIT_OFFSET_CAPTURE & $flags) {
            foreach ($chunks as &$chunk) {
                $str->string = $chunk[0];
                $chunk[0] = clone $str;
            }
        } else {
            foreach ($chunks as &$chunk) {
                $str->string = $chunk;
                $chunk = clone $str;
            }
        }

        return $chunks;
    }

    /**
     * @param string|string[] $prefix
     */
    public function startsWith($prefix): bool
    {
        if (!\is_array($prefix) && !$prefix instanceof \Traversable) {
            throw new \TypeError(sprintf('Method "%s()" must be overridden by class "%s" to deal with non-iterable values.', __FUNCTION__, \get_class($this)));
        }

        foreach ($prefix as $prefix) {
            if ($this->startsWith((string) $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return static
     */
    abstract public function title(bool $allWords = false): self;

    public function toByteString(string $toEncoding = null): ByteString
    {
        $b = new ByteString();

        $toEncoding = \in_array($toEncoding, ['utf8', 'utf-8', 'UTF8'], true) ? 'UTF-8' : $toEncoding;

        if (null === $toEncoding || $toEncoding === $fromEncoding = $this instanceof AbstractUnicodeString || preg_match('//u', $b->string) ? 'UTF-8' : 'Windows-1252') {
            $b->string = $this->string;

            return $b;
        }

        set_error_handler(static function ($t, $m) { throw new InvalidArgumentException($m); });

        try {
            try {
                $b->string = mb_convert_encoding($this->string, $toEncoding, 'UTF-8');
            } catch (InvalidArgumentException $e) {
                if (!\function_exists('iconv')) {
                    throw $e;
                }

                $b->string = iconv('UTF-8', $toEncoding, $this->string);
            }
        } finally {
            restore_error_handler();
        }

        return $b;
    }

    public function toCodePointString(): CodePointString
    {
        return new CodePointString($this->string);
    }

    public function toString(): string
    {
        return $this->string;
    }

    public function toUnicodeString(): UnicodeString
    {
        return new UnicodeString($this->string);
    }

    /**
     * @return static
     */
    abstract public function trim(string $chars = " \t\n\r\0\x0B\x0C\u{A0}\u{FEFF}"): self;

    /**
     * @return static
     */
    abstract public function trimEnd(string $chars = " \t\n\r\0\x0B\x0C\u{A0}\u{FEFF}"): self;

    /**
     * @return static
     */
    abstract public function trimStart(string $chars = " \t\n\r\0\x0B\x0C\u{A0}\u{FEFF}"): self;

    /**
     * @return static
     */
    public function truncate(int $length, string $ellipsis = ''): self
    {
        $stringLength = $this->length();

        if ($stringLength <= $length) {
            return clone $this;
        }

        $ellipsisLength = '' !== $ellipsis ? (new static($ellipsis))->length() : 0;

        if ($length < $ellipsisLength) {
            $ellipsisLength = 0;
        }

        $str = $this->slice(0, $length - $ellipsisLength);

        return $ellipsisLength ? $str->trimEnd()->append($ellipsis) : $str;
    }

    /**
     * @return static
     */
    abstract public function upper(): self;

    abstract public function width(bool $ignoreAnsiDecoration = true): int;

    /**
     * @return static
     */
    public function wordwrap(int $width = 75, string $break = "\n", bool $cut = false): self
    {
        $lines = '' !== $break ? $this->split($break) : [clone $this];
        $chars = [];
        $mask = '';

        if (1 === \count($lines) && '' === $lines[0]->string) {
            return $lines[0];
        }

        foreach ($lines as $i => $line) {
            if ($i) {
                $chars[] = $break;
                $mask .= '#';
            }

            foreach ($line->chunk() as $char) {
                $chars[] = $char->string;
                $mask .= ' ' === $char->string ? ' ' : '?';
            }
        }

        $string = '';
        $j = 0;
        $b = $i = -1;
        $mask = wordwrap($mask, $width, '#', $cut);

        while (false !== $b = strpos($mask, '#', $b + 1)) {
            for (++$i; $i < $b; ++$i) {
                $string .= $chars[$j];
                unset($chars[$j++]);
            }

            if ($break === $chars[$j] || ' ' === $chars[$j]) {
                unset($chars[$j++]);
            }

            $string .= $break;
        }

        $str = clone $this;
        $str->string = $string.implode('', $chars);

        return $str;
    }

    /**
     * @return static
     */
    abstract public function reverse(): self;

    /**
     * Returns the singular form of the string.
     *
     * If the method can't determine the form with certainty, an array of the
     * possible singulars is returned.
     *
     * @return static|static[] The singular form or an array of possible singular forms
     */
    public function singularize()
    {
        // Assume that plural and singular is identical
        $str = clone $this;

        $pluralRev = $str->reverse();
        $lowerPluralRev = $pluralRev->lower();

        // Check if the word is one which is not inflected, return early if so
        if (\in_array($lowerPluralRev->string, self::$uninflected, true)) {
            return $str;
        }

        $pluralLength = $lowerPluralRev->length();

        // The outer loop iterates over the entries of the plural table
        // The inner loop $j iterates over the characters of the plural suffix
        // in the plural table to compare them with the characters of the actual
        // given plural suffix
        foreach (self::$pluralMap as $map) {
            $suffix = $map[0];
            $suffixLength = $map[1];
            $j = 0;

            // Compare characters in the plural table and of the suffix of the
            // given plural one by one
            while ($suffix[$j] === $lowerPluralRev->slice($j, 1)->string) {
                // Let $j point to the next character
                ++$j;

                // Successfully compared the last character
                // Add an entry with the singular suffix to the singular array
                if ($j === $suffixLength) {
                    // Is there any character preceding the suffix in the plural string?
                    if ($j < $pluralLength) {
                        $nextIsVocal = false !== strpos('aeiou', $lowerPluralRev->slice($j, 1)->string);

                        if (!$map[2] && $nextIsVocal) {
                            // suffix may not succeed a vocal but next char is one
                            break;
                        }

                        if (!$map[3] && !$nextIsVocal) {
                            // suffix may not succeed a consonant but next char is one
                            break;
                        }
                    }

                    $newBase = $str->slice(0, $pluralLength - $suffixLength)->string;
                    $newSuffix = $map[4];

                    // Check whether the first character in the plural suffix
                    // is uppercased. If yes, uppercase the first character in
                    // the singular suffix too
                    $firstUpper = ($s = $pluralRev->slice($j - 1, 1))->string === $s->upper()->string;

                    if (\is_array($newSuffix)) {
                        $singulars = [];

                        foreach ($newSuffix as $newSuffixEntry) {
                            $singulars[] = new static($newBase.($firstUpper ? ucfirst($newSuffixEntry) : $newSuffixEntry));
                        }

                        return $singulars;
                    }

                    return new static($newBase.($firstUpper ? ucfirst($newSuffix) : $newSuffix));
                }

                // Suffix is longer than word
                if ($j === $pluralLength) {
                    break;
                }
            }
        }

        return $str;
    }

    /**
     * Returns the plural form of a word.
     *
     * If the method can't determine the form with certainty, an array of the
     * possible plurals is returned.
     *
     * @return static|static[] The plural form or an array of possible plural forms
     */
    public function pluralize()
    {
        $str = clone $this;

        $singularRev = $str->reverse();
        $lowerSingularRev = $singularRev->lower();

        // Check if the word is one which is not inflected, return early if so
        if (\in_array($lowerSingularRev->string, self::$uninflected, true)) {
            return $str;
        }

        $singularLength = $lowerSingularRev->length();

        // The outer loop iterates over the entries of the singular table
        // The inner loop $j iterates over the characters of the singular suffix
        // in the singular table to compare them with the characters of the actual
        // given singular suffix
        foreach (self::$singularMap as $map) {
            $suffix = $map[0];
            $suffixLength = $map[1];
            $j = 0;

            // Compare characters in the singular table and of the suffix of the
            // given plural one by one

            while ($suffix[$j] === $lowerSingularRev->slice($j, 1)->string) {
                // Let $j point to the next character
                ++$j;

                // Successfully compared the last character
                // Add an entry with the plural suffix to the plural array
                if ($j === $suffixLength) {
                    // Is there any character preceding the suffix in the plural string?
                    if ($j < $singularLength) {
                        $nextIsVocal = false !== strpos('aeiou', $lowerSingularRev->slice($j, 1)->string);

                        if (!$map[2] && $nextIsVocal) {
                            // suffix may not succeed a vocal but next char is one
                            break;
                        }

                        if (!$map[3] && !$nextIsVocal) {
                            // suffix may not succeed a consonant but next char is one
                            break;
                        }
                    }

                    $newBase = $str->slice(0, $singularLength - $suffixLength)->string;
                    $newSuffix = $map[4];

                    // Check whether the first character in the singular suffix
                    // is uppercased. If yes, uppercase the first character in
                    // the singular suffix too
                    $firstUpper = ($s = $singularRev->slice($j - 1, 1))->string === $s->upper()->string;

                    if (\is_array($newSuffix)) {
                        $plurals = [];

                        foreach ($newSuffix as $newSuffixEntry) {
                            $plurals[] = new static($newBase.($firstUpper ? ucfirst($newSuffixEntry) : $newSuffixEntry));
                        }

                        return $plurals;
                    }

                    return new static($newBase.($firstUpper ? ucfirst($newSuffix) : $newSuffix));
                }

                // Suffix is longer than word
                if ($j === $singularLength) {
                    break;
                }
            }
        }

        // Assume that plural is singular with a trailing `s`
        $str->string .= 's';

        return $str;
    }

    public function __sleep(): array
    {
        return ['string'];
    }

    public function __clone()
    {
        $this->ignoreCase = false;
    }

    public function __toString(): string
    {
        return $this->string;
    }
}

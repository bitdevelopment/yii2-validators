/**
 * @package   yii2-validators
 * @author    Milos Radojevic <crnimilos@gmail.com>
 * @copyright Copyright &copy; Milos Radojevic, 2015-2016
 * @version   1.0.0
 */
yii.validation.wordvalidator = function (value, messages, options) {
            var valid = false;
            
            var countWords = function (str, format, charlist) {
                    var len = str.length,
                      cl = charlist && charlist.length,
                      chr = '',
                      tmpStr = '',
                      i = 0,
                      c = '',
                      wArr = [],
                      wC = 0,
                      assoc = {},
                      aC = 0,
                      reg = '',
                      match = false;

                    // BEGIN STATIC
                    var _preg_quote = function(str) {
                      return (str + '')
                        .replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!<>\|\:])/g, '\\$1');
                    };
                    _getWholeChar = function(str, i) { // Use for rare cases of non-BMP characters
                      var code = str.charCodeAt(i);
                      if (code < 0xD800 || code > 0xDFFF) {
                        return str.charAt(i);
                      }
                      if (0xD800 <= code && code <= 0xDBFF) { // High surrogate (could change last hex to 0xDB7F to treat high private surrogates as single characters)
                        if (str.length <= (i + 1)) {
                          throw 'High surrogate without following low surrogate';
                        }
                        var next = str.charCodeAt(i + 1);
                        if (0xDC00 > next || next > 0xDFFF) {
                          throw 'High surrogate without following low surrogate';
                        }
                        return str.charAt(i) + str.charAt(i + 1);
                      }
                      // Low surrogate (0xDC00 <= code && code <= 0xDFFF)
                      if (i === 0) {
                        throw 'Low surrogate without preceding high surrogate';
                      }
                      var prev = str.charCodeAt(i - 1);
                      if (0xD800 > prev || prev > 0xDBFF) { // (could change last hex to 0xDB7F to treat high private surrogates as single characters)
                        throw 'Low surrogate without preceding high surrogate';
                      }
                      return false; // We can pass over low surrogates now as the second component in a pair which we have already processed
                    };
                    // END STATIC
                    if (cl) {
                      reg = '^(' + _preg_quote(_getWholeChar(charlist, 0));
                      for (i = 1; i < cl; i++) {
                        if ((chr = _getWholeChar(charlist, i)) === false) {
                          continue;
                        }
                        reg += '|' + _preg_quote(chr);
                      }
                      reg += ')$';
                      reg = new RegExp(reg);
                    }

                    for (i = 0; i < len; i++) {
                      if ((c = _getWholeChar(str, i)) === false) {
                        continue;
                      }
                      match = c.search(RegExp('^([a-z]+)?$','ig')) !== -1 || (reg && c.search(reg) !== -1) || ((i !== 0 && i !== len - 1) && c === '-') || // No hyphen at beginning or end unless allowed in charlist (or locale)
                      (i !== 0 && c === "'"); // No apostrophe at beginning unless allowed in charlist (or locale)
                      if (match) {
                        if (tmpStr === '' && format === 2) {
                          aC = i;
                        }
                        tmpStr = tmpStr + c;
                      }
                      if (i === len - 1 || !match && tmpStr !== '') {
                        if (format !== 2) {
                          wArr[wArr.length] = tmpStr;
                        } else {
                          assoc[aC] = tmpStr;
                        }
                        tmpStr = '';
                        wC++;
                      }
                    }

                    if (!format) {
                      return wC;
                    } else if (format === 1) {
                      return wArr;
                    } else if (format === 2) {
                      return assoc;
                    }
                    throw 'You have supplied an incorrect format';
            };

            var count = countWords(value);

            var remaining = options.max-count;
            var required = options.min-count;
            if (remaining<0) {
                var message = options.maxWordsExceeded.replace('{words}',remaining);
            } else if(required>=0) {
                var message = options.minWordsRequired.replace('{words}',required);
            } else {
                valid = true;
                var message = "";
            }          
            message = message.replace('{min}',options.min);
            if (!valid) {
                yii.validation.addMessage(messages, message, value);
            }
};
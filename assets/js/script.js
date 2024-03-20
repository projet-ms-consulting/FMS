console.log('Hello World!');

const div = document.querySelector('.text');
const text = 'reelance';
const text2 = 'anagement';
const text3 = 'ystem';

setTimeout(() => {
  function textTypingEffect(element, text, i = 0) {
    element.textContent += text[i];

    if (i === text.length - 1) {
      return;
    }

    setTimeout(() => textTypingEffect(element, text, i + 1), 100);
  }

  textTypingEffect(div, text);
  setTimeout(
    () => textTypingEffect(document.querySelector('.text2'), text2),
    150 * text.length
  );
  setTimeout(
    () => textTypingEffect(document.querySelector('.text3'), text3),
    150 * (text.length + text2.length)
  );
}, 50);

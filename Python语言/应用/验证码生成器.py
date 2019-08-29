import random

def code(code_len=5):
	chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
	code = ''
	for i in range(code_len):
		index = random.randint(0, len(chars)-1)
		code += chars[index]
	return code

print(code(6))
